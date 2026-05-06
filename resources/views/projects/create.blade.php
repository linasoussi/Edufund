@extends('layouts.app')
@section('title', 'Créer un projet — EduFund')

@push('styles')
<style>
.create-layout { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; }
@media (max-width: 1024px) { .create-layout { grid-template-columns: 1fr; } }

.ai-panel {
    position: sticky;
    top: 80px;
    height: fit-content;
}

.score-ring {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
}
.score-ring svg { position: absolute; top: 0; left: 0; transform: rotate(-90deg); }
.score-text { position: relative; z-index: 1; text-align: center; }

.suggestion-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: 10px;
    background: #F9FAFB;
    font-size: 0.8rem;
    color: #374151;
    margin-bottom: 0.5rem;
}

.char-count {
    font-size: 0.7rem;
    color: #9CA3AF;
    text-align: right;
    margin-top: 0.25rem;
}

.section-card {
    background: white;
    border-radius: 20px;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(108,59,238,0.08);
    box-shadow: 0 4px 20px rgba(108,59,238,0.06);
}
.section-title {
    font-family: 'Clash Display', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    color: #111827;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-primary">Tableau de bord</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>Créer un projet</span>
        </div>
        <h1 class="font-display text-3xl font-bold text-gray-900">🚀 Créer votre projet</h1>
        <p class="text-gray-500 mt-1">Notre IA analyse votre projet en temps réel pour maximiser vos chances de succès</p>
    </div>

    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="project-form">
        @csrf

        <div class="create-layout">

            <!-- LEFT: Form -->
            <div>
                <!-- Informations de base -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 text-sm">📝</span>
                        Informations de base
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="form-label">Titre du projet <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="project-title" value="{{ old('title') }}"
                                placeholder="Un titre accrocheur et mémorable..." class="form-input" maxlength="100"
                                oninput="updateCharCount(this, 'title-count', 100); debouncePrediction()">
                            <div class="char-count"><span id="title-count">0</span>/100</div>
                            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label">Résumé du projet <span class="text-red-500">*</span></label>
                            <textarea name="short_description" id="project-short-desc" rows="2"
                                placeholder="En 1-2 phrases, expliquez l'essentiel de votre projet..."
                                class="form-input resize-none" maxlength="500"
                                oninput="updateCharCount(this, 'short-count', 500); debouncePrediction()">{{ old('short_description') }}</textarea>
                            <div class="char-count"><span id="short-count">0</span>/500</div>
                            @error('short_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label">Description complète <span class="text-red-500">*</span></label>
                            <textarea name="description" id="project-desc" rows="8"
                                placeholder="Décrivez votre projet en détail : objectifs, impact, plan d'action, équipe, budget..."
                                class="form-input resize-none" style="min-height: 200px"
                                oninput="updateCharCount(this, 'desc-count', 5000); debouncePrediction()">{{ old('description') }}</textarea>
                            <div class="char-count"><span id="desc-count">0</span>/5000 — Minimum recommandé : 500 caractères</div>
                            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Catégorie <span class="text-red-500">*</span></label>
                                <select name="category" id="project-category" class="form-input" onchange="debouncePrediction()">
                                    <option value="">Sélectionner...</option>
                                    @foreach(['Technologie & Innovation', 'Environnement & Énergie', 'Santé & Bien-être', 'Éducation & Formation', 'Art & Culture', 'Social & Humanitaire', 'Agriculture & Alimentation', 'Autre'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="form-label">Tags (séparés par virgules)</label>
                                <input type="text" name="tags" value="{{ old('tags') }}"
                                    placeholder="innovation, mobile, IA..." class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campagne -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 text-sm">💰</span>
                        Campagne de financement
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">Objectif financier (TND) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="funding_goal" id="project-goal" value="{{ old('funding_goal') }}"
                                    placeholder="5000" min="100" step="50" class="form-input pr-14"
                                    oninput="debouncePrediction()">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">TND</span>
                            </div>
                            @error('funding_goal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Date limite <span class="text-red-500">*</span></label>
                            <input type="date" name="deadline" id="project-deadline" value="{{ old('deadline') }}"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-input"
                                onchange="debouncePrediction()">
                            @error('deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-xl text-xs text-amber-800 flex items-start gap-2" style="background: #FEF3C7">
                        <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                        <span>💡 Conseil : Les campagnes de 30 à 60 jours avec un objectif entre 1 000 et 10 000 TND ont les meilleurs taux de réussite.</span>
                    </div>
                </div>

                <!-- Media -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 text-sm">🖼️</span>
                        Médias
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="form-label">Image de couverture</label>
                            <label class="flex flex-col items-center justify-center w-full h-40 rounded-2xl border-2 border-dashed border-gray-200 hover:border-primary cursor-pointer transition-colors group" id="cover-label">
                                <div class="text-center" id="cover-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-primary transition-colors mb-2 block"></i>
                                    <p class="text-sm text-gray-500">Cliquez ou glissez une image (max 5MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP recommandé</p>
                                </div>
                                <img id="cover-preview" src="" alt="" class="hidden w-full h-full object-cover rounded-2xl">
                                <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewCover(this)">
                            </label>
                        </div>

                        <div>
                            <label class="form-label">URL Vidéo YouTube/Vimeo <span class="text-gray-400 font-normal">(optionnel)</span></label>
                            <input type="url" name="video_url" value="{{ old('video_url') }}"
                                placeholder="https://youtube.com/watch?v=..." class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Images galerie <span class="text-gray-400 font-normal">(optionnel, max 5)</span></label>
                            <input type="file" name="gallery_images[]" accept="image/*" multiple class="form-input py-2">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-3">
                    <button type="submit" name="action" value="draft" class="btn-outline flex-1 justify-center py-3.5">
                        <i class="fas fa-save"></i> Sauvegarder en brouillon
                    </button>
                    <button type="submit" name="action" value="publish" class="btn-primary flex-1 justify-center py-3.5">
                        <i class="fas fa-rocket"></i> Créer et publier
                    </button>
                </div>
            </div>

            <!-- RIGHT: AI Panel -->
            <div class="ai-panel">
                <!-- AI Score Card -->
                <div class="card p-5 mb-4" style="background: linear-gradient(135deg, #0A0A0F, #1C1028)">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #6C3BEE, #F59E0B)">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-display font-bold text-white text-sm">Prédiction IA</p>
                            <p class="text-gray-400 text-xs">Analyse en temps réel</p>
                        </div>
                        <div class="ml-auto">
                            <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse"></span>
                        </div>
                    </div>

                    <!-- Score Ring -->
                    <div class="score-ring mb-3" id="score-ring-container">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#ffffff15" stroke-width="8"/>
                            <circle id="score-circle" cx="50" cy="50" r="42" fill="none"
                                stroke="url(#scoreGrad)" stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="264"
                                stroke-dashoffset="264"
                                style="transition: stroke-dashoffset 1s ease"/>
                            <defs>
                                <linearGradient id="scoreGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#6C3BEE"/>
                                    <stop offset="100%" style="stop-color:#F59E0B"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="score-text">
                            <div id="score-value" class="font-display font-bold text-2xl text-white">--</div>
                            <div class="text-gray-400 text-xs">score</div>
                        </div>
                    </div>

                    <div id="score-level" class="text-center mb-4 text-gray-400 text-sm">Remplissez le formulaire...</div>

                    <!-- Suggestions -->
                    <div id="ai-suggestions" class="space-y-1.5 hidden">
                        <p class="text-xs font-bold text-gray-300 uppercase tracking-wide mb-2">💡 Suggestions IA</p>
                        <div id="suggestions-list"></div>
                    </div>

                    <button type="button" id="analyze-btn" onclick="runPrediction()"
                        class="btn-primary w-full justify-center mt-3 text-xs py-2.5">
                        <i class="fas fa-magic"></i> Analyser mon projet
                    </button>
                </div>

                <!-- Strengths / Weaknesses -->
                <div id="analysis-details" class="hidden">
                    <div class="card p-4 mb-3">
                        <p class="text-xs font-bold text-green-600 mb-2">✅ Points forts</p>
                        <div id="strengths-list"></div>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-bold text-red-500 mb-2">⚠️ Points à améliorer</p>
                        <div id="weaknesses-list"></div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card p-4 mt-4">
                    <p class="font-display font-bold text-sm text-gray-800 mb-3">🎯 Astuces rapides</p>
                    <ul class="space-y-2 text-xs text-gray-600">
                        <li class="flex items-start gap-2"><span class="text-purple-500">→</span> Utilisez un titre entre 6-10 mots</li>
                        <li class="flex items-start gap-2"><span class="text-purple-500">→</span> Décrivez l'impact social de votre projet</li>
                        <li class="flex items-start gap-2"><span class="text-purple-500">→</span> Fixez un objectif réaliste et justifié</li>
                        <li class="flex items-start gap-2"><span class="text-purple-500">→</span> Ajoutez une vidéo pour +40% de conversions</li>
                        <li class="flex items-start gap-2"><span class="text-purple-500">→</span> Campagne de 30-60 jours idéale</li>
                    </ul>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateCharCount(el, countId, max) {
    const len = el.value.length;
    document.getElementById(countId).textContent = len;
    document.getElementById(countId).style.color = len > max * 0.9 ? '#EF4444' : '#9CA3AF';
}

function previewCover(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('cover-preview').src = e.target.result;
            document.getElementById('cover-preview').classList.remove('hidden');
            document.getElementById('cover-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

let predictionTimeout;
function debouncePrediction() {
    clearTimeout(predictionTimeout);
    predictionTimeout = setTimeout(runPrediction, 2000);
}

async function runPrediction() {
    const title = document.getElementById('project-title').value;
    const desc = document.getElementById('project-desc').value;
    const shortDesc = document.getElementById('project-short-desc').value;
    const category = document.getElementById('project-category').value;
    const goal = parseFloat(document.getElementById('project-goal').value) || 0;
    const deadline = document.getElementById('project-deadline').value;

    const btn = document.getElementById('analyze-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyse...';
    btn.disabled = true;

    // Petit délai pour l'effet visuel
    setTimeout(() => {
        let score = 20;      // score de base minimal
        let strengths = [];
        let weaknesses = [];
        let suggestions = [];

        // -------------------- TITRE --------------------
        if (title) {
            if (title.length >= 10 && title.length <= 80) {
                score += 15;
                strengths.push("✅ Titre de bonne longueur (10-80 caractères)");
            } else {
                weaknesses.push("❌ Titre trop court ou trop long (idéal : 10-80 caractères)");
                suggestions.push("📝 Un titre percutant fait 6 à 12 mots");
            }
        } else {
            weaknesses.push("❌ Titre manquant");
            suggestions.push("✏️ Ajoutez un titre accrocheur");
        }

        // -------------------- RÉSUMÉ --------------------
        if (shortDesc) {
            if (shortDesc.length >= 100) {
                score += 10;
                strengths.push("✅ Résumé suffisamment détaillé");
            } else {
                weaknesses.push("❌ Résumé trop court (min 100 caractères)");
                suggestions.push("📄 Développez votre résumé en 2-3 phrases");
            }
        } else {
            weaknesses.push("❌ Résumé manquant");
            suggestions.push("📝 Rédigez un résumé du projet");
        }

        // -------------------- DESCRIPTION --------------------
        if (desc) {
            if (desc.length > 800) {
                score += 20;
                strengths.push("✅ Description très complète (> 800 caractères)");
            } else if (desc.length >= 400) {
                score += 10;
                strengths.push("✅ Description correcte, peut être enrichie");
                suggestions.push("📖 Ajoutez plus de détails sur l'impact, l'équipe, le budget");
            } else {
                weaknesses.push("❌ Description trop courte (< 400 caractères)");
                suggestions.push("📖 Rédigez une description détaillée (500+ caractères)");
            }
        } else {
            weaknesses.push("❌ Description manquante");
            suggestions.push("📝 Décrivez votre projet : objectifs, impact, plan d'action");
        }

        // -------------------- CATÉGORIE --------------------
        if (category && category !== "") {
            score += 5;
            strengths.push("🏷️ Catégorie sélectionnée");
        } else {
            weaknesses.push("❌ Catégorie non choisie");
            suggestions.push("🎯 Choisissez une catégorie pour mieux cibler");
        }

        // -------------------- OBJECTIF --------------------
        if (goal > 0) {
            if (goal >= 1000 && goal <= 10000) {
                score += 15;
                strengths.push("💰 Objectif financier réaliste (1 000 – 10 000 TND)");
            } else if (goal < 1000) {
                weaknesses.push("⚠️ Objectif très bas (< 1000 TND)");
                suggestions.push("🔧 Un objectif trop bas manque de crédibilité");
            } else if (goal > 20000) {
                weaknesses.push("⚠️ Objectif élevé (> 20 000 TND)");
                suggestions.push("🎯 Réduisez l'objectif ou justifiez-le très clairement");
            } else {
                score += 5;
                strengths.push("📊 Objectif acceptable");
            }
        } else {
            weaknesses.push("❌ Objectif non renseigné");
            suggestions.push("💰 Fixez un objectif financier (ex: 5000 TND)");
        }

        // -------------------- DATE LIMITE --------------------
        if (deadline) {
            const today = new Date();
            const deadlineDate = new Date(deadline);
            const daysLeft = Math.ceil((deadlineDate - today) / (1000 * 60 * 60 * 24));
            if (daysLeft >= 30 && daysLeft <= 60) {
                score += 15;
                strengths.push(`⏱️ Durée de campagne optimale : ${daysLeft} jours`);
            } else if (daysLeft > 0 && daysLeft < 30) {
                weaknesses.push(`⚠️ Campagne trop courte (${daysLeft} jours)`);
                suggestions.push("📅 Prolongez à au moins 30 jours");
            } else if (daysLeft > 60) {
                weaknesses.push(`⏳ Campagne très longue (${daysLeft} jours)`);
                suggestions.push("📅 Réduisez la durée à 60 jours max");
            } else if (daysLeft <= 0) {
                weaknesses.push("❌ Date limite déjà passée");
                suggestions.push("📅 Choisissez une date future (30-60 jours)");
            }
        } else {
            weaknesses.push("❌ Date limite non renseignée");
            suggestions.push("📅 Sélectionnez une date de fin de campagne");
        }

        // Plafonnement du score
        score = Math.min(100, Math.max(0, score));
        let level = "";
        if (score >= 80) level = "excellent";
        else if (score >= 65) level = "bon";
        else if (score >= 45) level = "moyen";
        else level = "faible";

        // Suggestions par défaut si aucune
        if (suggestions.length === 0) {
            suggestions.push("🎬 Ajoutez une vidéo (+40% de conversions)");
            suggestions.push("📢 Partagez sur les réseaux sociaux");
            suggestions.push("🤝 Proposez des récompenses attractives");
        }

        const analysis = {
            score: score,
            level: level,
            strengths: strengths.slice(0, 4),
            weaknesses: weaknesses.slice(0, 4),
            suggestions: suggestions.slice(0, 5),
            analysis: `Score estimé : ${score}% – Projet ${level}.`
        };

        // Affiche dans le panneau de droite
        displayAnalysis(analysis);

        btn.innerHTML = '<i class="fas fa-magic"></i> Analyser mon projet';
        btn.disabled = false;
    }, 300);
}

function displayAnalysis(a) {
    // Score ring
    const score = a.score || 0;
    const circumference = 264;
    const offset = circumference - (score / 100) * circumference;
    document.getElementById('score-circle').style.strokeDashoffset = offset;
    document.getElementById('score-value').textContent = score + '%';

    // Level
    const levelMap = { faible: '🔴 Faible', moyen: '🟡 Moyen', bon: '🟢 Bon', excellent: '⭐ Excellent' };
    document.getElementById('score-level').textContent = levelMap[a.level] || a.level;
    document.getElementById('score-level').style.color = {faible:'#EF4444', moyen:'#F59E0B', bon:'#10B981', excellent:'#6C3BEE'}[a.level] || 'white';

    // Suggestions
    if (a.suggestions && a.suggestions.length) {
        document.getElementById('ai-suggestions').classList.remove('hidden');
        document.getElementById('suggestions-list').innerHTML = a.suggestions
            .map(s => `<div class="suggestion-item"><span class="text-purple-500 text-xs mt-0.5">💡</span><span>${s}</span></div>`)
            .join('');
    }

    // Strengths / Weaknesses
    document.getElementById('analysis-details').classList.remove('hidden');
    if (a.strengths) {
        document.getElementById('strengths-list').innerHTML = a.strengths
            .map(s => `<div class="suggestion-item text-green-700"><span>✓</span><span>${s}</span></div>`)
            .join('');
    }
    if (a.weaknesses) {
        document.getElementById('weaknesses-list').innerHTML = a.weaknesses
            .map(s => `<div class="suggestion-item text-red-600"><span>✗</span><span>${s}</span></div>`)
            .join('');
    }
}

// Init char counts
['project-title', 'project-short-desc', 'project-desc'].forEach(id => {
    const el = document.getElementById(id);
    if (el && el.value) {
        const map = { 'project-title': ['title-count', 100], 'project-short-desc': ['short-count', 500], 'project-desc': ['desc-count', 5000] };
        updateCharCount(el, map[id][0], map[id][1]);
    }
});
</script>
@endpush
