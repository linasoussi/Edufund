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
     * Modèle actuel : Llama 3.3 70B (Dernière version stable)
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

        // MODÈLE ACTUALISÉ (Llama 3.3 est la version actuelle recommandée)
        // Si cela échoue à l'avenir, essayez : 'llama-3.1-8b-instant'
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

            // Gestion des erreurs API
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
     * Prédiction du succès d’un projet
     * On utilise un modèle plus léger et très stable pour l'analyse de données (JSON)
     */
    public function predictSuccess(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'category' => 'nullable|string',
            'funding_goal' => 'required|numeric',
            'deadline' => 'required|date',
        ]);

        $daysLeft = now()->diffInDays($request->deadline, false);
        $descLen = strlen($request->description);
        $fallback = $this->localPrediction($request, $daysLeft, $descLen);

        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['success' => true, 'analysis' => $fallback]);
        }

        // MODÈLE STABLE POUR L'ANALYSE (8b-instant est très fiable pour le JSON)
        $model = 'llama-3.1-8b-instant';
        
        $systemPrompt = "Tu es un expert en analyse de projets de crowdfunding étudiant. Retourne UNIQUEMENT un objet JSON valide, sans markdown, sans backticks, avec ces clés : score (0-100), level (faible/moyen/bon/excellent), strengths (array 2-4 strings), weaknesses (array 2-4 strings), suggestions (array 3-5 strings), analysis (courte phrase). Réponds en français.";

        $userMsg = "Analyse ce projet :
Titre: {$request->title}
Catégorie: " . ($request->category ?? 'Non spécifiée') . "
Description courte: " . ($request->short_description ?? '') . "
Description: " . substr($request->description, 0, 1200) . "
Objectif: {$request->funding_goal} TND
Jours restants: {$daysLeft}
Longueur description: {$descLen} caractères";

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
                'temperature' => 0.4,
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
            Log::error('Groq predict exception: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'analysis' => $fallback]);
    }

    /**
     * Fallback local (analyse sans API)
     */
    private function localPrediction($request, int $daysLeft, int $descLen): array
    {
        $score = 45;
        $strengths = [];
        $weaknesses = [];
        $suggestions = [];

        if ($descLen > 500) { $score += 15; $strengths[] = "Description détaillée"; }
        else { $score -= 10; $weaknesses[] = "Description trop courte"; $suggestions[] = "Ajoutez plus de détails (min 500 caractères)"; }

        if ($request->funding_goal <= 5000) { $score += 10; $strengths[] = "Objectif accessible"; }
        elseif ($request->funding_goal > 15000) { $score -= 10; $weaknesses[] = "Objectif élevé"; $suggestions[] = "Réduisez l'objectif à moins de 10 000 TND"; }

        if ($daysLeft >= 30 && $daysLeft <= 60) { $score += 10; $strengths[] = "Durée idéale (30-60 jours)"; }
        elseif ($daysLeft < 15) { $score -= 15; $weaknesses[] = "Délai trop court"; $suggestions[] = "Allongez à au moins 30 jours"; }

        if (!empty($request->category)) { $score += 5; $strengths[] = "Catégorie définie"; }
        else { $weaknesses[] = "Catégorie manquante"; $suggestions[] = "Choisissez une catégorie"; }

        $suggestions[] = "Ajoutez une vidéo de présentation";
        $suggestions[] = "Partagez sur les réseaux sociaux";
        $score = min(100, max(10, $score));
        $level = $score >= 75 ? 'excellent' : ($score >= 60 ? 'bon' : ($score >= 40 ? 'moyen' : 'faible'));

        return [
            'score' => $score,
            'level' => $level,
            'strengths' => $strengths ?: ['Potentiel identifiable'],
            'weaknesses' => $weaknesses ?: ['Quelques points à améliorer'],
            'suggestions' => $suggestions,
            'analysis' => "Score estimé localement : $score% – Projet $level."
        ];
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