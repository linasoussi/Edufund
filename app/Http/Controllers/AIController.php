<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Chatbot IA Polyvalent via Groq
     */
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'success' => true,
                'message' => "⚠️ Clé API Groq manquante. Ajoutez `GROQ_API_KEY` dans votre fichier `.env`."
            ]);
        }

        $model = 'llama-3.3-70b-versatile'; 

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es une intelligence artificielle avancée, amicale et extrêmement polyvalente. Tu peux converser sur n\'importe quel sujet : programmation, rédaction, sciences, culture générale, conseils, humour ou philosophie. Adopte un ton naturel et engageant. Réponds toujours en français, sauf si l\'utilisateur t\'adresse la parole dans une autre langue.'],
                    ['role' => 'user', 'content' => $request->message],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1024,
            ]);

            if ($response->successful()) {
                $reply = $response->json()['choices'][0]['message']['content'] ?? "Désolé, je n'ai pas de réponse.";
                return response()->json(['success' => true, 'message' => $reply]);
            }

            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $errorBody = $response->json();
            $errorMsg = $errorBody['error']['message'] ?? 'Erreur inconnue';
            return response()->json([
                'success' => true,
                'message' => "🤖 Erreur API Groq : $errorMsg. Modèle utilisé : $model"
            ]);

        } catch (\Exception $e) {
            Log::error('Groq exception: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => "🤖 Impossible de contacter l'assistant IA."
            ]);
        }
    }

    /**
     * Prédiction Live (Progressive)
     * Commence à 0% et augmente à chaque champ rempli.
     * Utilisation d'un algorithme local pour la rapidité (pas d'appel API ici).
     */
    public function predictSuccess(Request $request)
    {
        // IMPORTANT : On change 'required' par 'nullable' pour accepter les formulaires incomplets
        $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'funding_goal' => 'nullable|numeric',
            'deadline' => 'nullable|date',
        ]);

        $score = 0; // Le score commence à 0
        $suggestions = [];
        $strengths = [];

        // 1. Vérification du TITRE (+25 points max)
        if (!empty($request->title)) {
            $score += 15; // Points de base pour l'existence
            if (strlen($request->title) > 10) {
                $score += 10;
                $strengths[] = "Titre présent et de bonne longueur";
            } else {
                $suggestions[] = "Le titre est un peu court (essayez 10+ caractères)";
            }
        } else {
            $suggestions[] = "Ajoutez un titre à votre projet";
        }

        // 2. Vérification de la DESCRIPTION (+35 points max)
        if (!empty($request->description)) {
            $score += 10; // Points de base
            $len = strlen($request->description);
            
            if ($len > 200) { $score += 10; }
            if ($len > 500) { $score += 15; } 
            
            if ($len > 500) {
                $strengths[] = "Description détaillée";
            } else {
                $suggestions[] = "Décrivez plus votre projet (min 500 caractères)";
            }
        } else {
            $suggestions[] = "La description est manquante";
        }

        // 3. Vérification de la CATÉGORIE (+10 points)
        if (!empty($request->category)) {
            $score += 10;
            $strengths[] = "Catégorie définie";
        } else {
            $suggestions[] = "Choisissez une catégorie";
        }

        // 4. Vérification de l'OBJECTIF FINANCIER (+20 points max)
        if (!empty($request->funding_goal)) {
            $score += 10; // Points de base
            
            // Logique : objectif modeste = plus facile à atteindre
            if ($request->funding_goal <= 5000) { 
                $score += 10; 
                $strengths[] = "Objectif réaliste";
            } elseif ($request->funding_goal > 20000) {
                $suggestions[] = "Objectif très élevé, risque d'échec";
            } else {
                $score += 5; // Moyen
            }
        } else {
            $suggestions[] = "Définissez un objectif financier";
        }

        // 5. Vérification de la DATE LIMITE (+10 points max)
        if (!empty($request->deadline)) {
            $score += 5; // Points de base
            
            try {
                $daysLeft = now()->diffInDays($request->deadline, false);
                
                if ($daysLeft >= 30 && $daysLeft <= 60) {
                    $score += 5;
                    $strengths[] = "Durée de campagne idéale";
                } elseif ($daysLeft < 15) {
                    $suggestions[] = "Délai trop court (min 30j conseillé)";
                } elseif ($daysLeft > 90) {
                    $suggestions[] = "Délai très long (perte d'urgence)";
                }
            } catch (\Exception $e) {
                // Ignore date errors
            }
        } else {
            $suggestions[] = "Fixez une date de fin de campagne";
        }

        // --- FINALISATION DU SCORE ---

        // On s'assure que le score reste entre 0 et 100
        $score = min(100, max(0, $score));

        // Détermination du niveau textuel
        $level = 'Commencez...';
        if ($score > 10) $level = 'Ébauche';
        if ($score > 30) $level = 'En cours';
        if ($score > 50) $level = 'Prometteur';
        if ($score > 70) $level = 'Bon';
        if ($score > 90) $level = 'Excellent !';

        return response()->json([
            'success' => true,
            'analysis' => [
                'score' => $score,
                'level' => $level,
                'strengths' => $strengths ?: ['Encore aucun point fort'],
                'weaknesses' => [], // On utilise surtout les suggestions
                'suggestions' => $suggestions,
                'analysis' => "Score actuel : $score%."
            ]
        ]);
    }

    /**
     * Nouvelle méthode : Analyse approfondie par l'IA (Uniquement à la fin)
     * Cette méthode est appelée quand l'utilisateur clique sur "Analyser avec l'IA"
     * pour avoir de vrais conseils profonds, pas juste un score.
     */
    public function deepAnalyze(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'nullable|string',
            'funding_goal' => 'required|numeric',
            'deadline' => 'required|date',
        ]);

        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Clé API manquante']);
        }

        $model = 'llama-3.1-8b-instant';
        
        $systemPrompt = "Tu es un expert en crowdfunding. Analyse ce projet et donne un score réaliste (0-100). Retourne UN JSON avec: score, level, strengths (array), weaknesses (array), suggestions (array), analysis (string).";

        $userMsg = "Projet: {$request->title}, Desc: {$request->description}, Goal: {$request->funding_goal}, Deadline: {$request->deadline}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMsg],
                ],
                'temperature' => 0.5,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $text = $response->json()['choices'][0]['message']['content'] ?? '';
                $clean = preg_replace('/```json|```/i', '', $text);
                $analysis = json_decode(trim($clean), true);
                if (json_last_error() === JSON_ERROR_NONE && isset($analysis['score'])) {
                    return response()->json(['success' => true, 'analysis' => $analysis]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Deep analyze error: ' . $e->getMessage());
        }

        return response()->json(['success' => false, 'message' => 'Erreur IA']);
    }

    public function saveAnalysis(Request $request, Project $project)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'suggestions' => 'nullable|array',
        ]);

        $project->update([
            'success_score' => $request->score,
            'ai_suggestions' => json_encode($request->suggestions ?? []),
        ]);

        return response()->json(['success' => true]);
    }
}