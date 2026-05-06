@extends('layouts.app')
@section('title', 'EduFund — Donnez vie à vos projets étudiants')

@push('styles')
<style>
.hero-bg {
    background: linear-gradient(135deg, #0A0A0F 0%, #1C1028 50%, #0D1117 100%);
    position: relative;
    overflow: hidden;
}
.hero-bg::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(ellipse at 30% 40%, rgba(108,59,238,0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 60%, rgba(245,158,11,0.1) 0%, transparent 50%);
}
.floating-card {
    animation: float 6s ease-in-out infinite;
}
.floating-card:nth-child(2) { animation-delay: 1.5s; }
.floating-card:nth-child(3) { animation-delay: 3s; }
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
}
.stat-counter { font-feature-settings: 'tnum'; }
.category-tag {
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    border: 2px solid #E5E7EB;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}
.category-tag:hover, .category-tag.active {
    border-color: #6C3BEE;
    background: #EDE9FE;
    color: #6C3BEE;
}
.role-card {
    border-radius: 24px;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 2px solid transparent;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.role-card::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.3s;
    border-radius: 22px;
}
.role-card:hover { transform: translateY(-8px); }
.role-card.etudiant { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); border-color: #A78BFA; }
.role-card.etudiant::before { background: linear-gradient(135deg, #6C3BEE22, #8B5CF622); }
.role-card.contributeur { background: linear-gradient(135deg, #FEF3C7, #FDE68A); border-color: #FCD34D; }
.role-card.partenaire { background: linear-gradient(135deg, #D1FAE5, #A7F3D0); border-color: #34D399; }
</style>
@endpush

@section('content')

<!-- HERO SECTION -->
<section class="hero-bg text-white py-24 relative">
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Text -->
            <div class="animate-fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold mb-6"
                    style="background: rgba(108,59,238,0.2); border: 1px solid rgba(108,59,238,0.4); color: #A78BFA">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Plateforme IA-powered de crowdfunding étudiant
                </div>

                <h1 class="font-display text-5xl md:text-6xl font-bold leading-tight mb-6">
                    Donnez vie à vos<br>
                    <span style="background: linear-gradient(135deg,#A78BFA,#FCD34D); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">
                        projets étudiants
                    </span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    EduFund connecte les étudiants innovants avec des contributeurs et partenaires. 
                    Financez votre projet avec l'aide de notre <strong class="text-purple-300">assistant IA</strong>.
                </p>

                <div class="flex flex-wrap gap-4 mb-12">
                    <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-3.5">
                        <i class="fas fa-rocket"></i> Lancer mon projet
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn-outline text-base px-8 py-3.5" style="color:white; border-color:rgba(255,255,255,0.3)">
                        <i class="fas fa-search"></i> Explorer les projets
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <p class="stat-counter font-display text-3xl font-bold gradient-text">{{ number_format($stats['projects']) }}+</p>
                        <p class="text-gray-400 text-sm mt-1">Projets lancés</p>
                    </div>
                    <div>
                        <p class="stat-counter font-display text-3xl font-bold gradient-text">{{ number_format($stats['raised'], 0, ',', ' ') }}</p>
                        <p class="text-gray-400 text-sm mt-1">TND collectés</p>
                    </div>
                    <div>
                        <p class="stat-counter font-display text-3xl font-bold gradient-text">{{ number_format($stats['contributors']) }}+</p>
                        <p class="text-gray-400 text-sm mt-1">Contributeurs</p>
                    </div>
                </div>
            </div>

            <!-- Right: Floating cards -->
            <div class="hidden lg:flex flex-col gap-4 items-end">
                <div class="floating-card glass rounded-2xl p-4 w-72">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/30 flex items-center justify-center">
                            <i class="fas fa-robot text-purple-300"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Assistant IA</p>
                            <p class="text-xs text-gray-400">Analyse en cours...</p>
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-xs text-gray-300">
                        🎯 Score de succès: <strong class="text-yellow-400">82%</strong><br>
                        💡 "Réduisez l'objectif à 3000 TND"<br>
                        ✅ Description de qualité détectée
                    </div>
                </div>

                <div class="floating-card glass rounded-2xl p-4 w-64 mr-8">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-white">Projet Tech</p>
                        <span class="badge badge-green text-xs">Financé 🎉</span>
                    </div>
                    <div class="progress-bar mb-2">
                        <div class="progress-fill" style="width: 94%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>4 700 TND</span>
                        <span class="text-green-400 font-bold">94%</span>
                    </div>
                </div>

                <div class="floating-card glass rounded-2xl p-4 w-72">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2">
                            @for($i=1; $i<=4; $i++)
                            <div class="w-8 h-8 rounded-full border-2 border-gray-800 flex items-center justify-center text-xs font-bold"
                                style="background: hsl({{ $i*60 }}, 70%, 60%)">{{ chr(64+$i) }}</div>
                            @endfor
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">{{ $stats['contributors'] }} contributeurs actifs</p>
                            <p class="text-xs text-gray-400">rejoignent cette semaine</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-14">
            <p class="text-primary font-semibold text-sm uppercase tracking-widest mb-2">Simple & Rapide</p>
            <h2 class="font-display text-4xl font-bold text-gray-900">Comment ça fonctionne ?</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['icon' => 'fas fa-user-plus', 'step' => '01', 'title' => 'Inscrivez-vous', 'desc' => 'Créez votre compte étudiant, contributeur ou partenaire en 2 minutes.', 'color' => '#6C3BEE'],
                ['icon' => 'fas fa-robot', 'step' => '02', 'title' => 'Créez avec l\'IA', 'desc' => 'Notre assistant IA vous aide à rédiger et optimiser votre projet pour maximiser vos chances.', 'color' => '#F59E0B'],
                ['icon' => 'fas fa-hand-holding-heart', 'step' => '03', 'title' => 'Collectez des fonds', 'desc' => 'Publiez votre projet et recevez des contributions de toute la communauté.', 'color' => '#10B981'],
            ] as $step)
            <div class="text-center group">
                <div class="w-20 h-20 rounded-2xl mx-auto mb-5 flex items-center justify-center text-white text-2xl shadow-lg group-hover:scale-110 transition-transform"
                    style="background: linear-gradient(135deg, {{ $step['color'] }}, {{ $step['color'] }}99)">
                    <i class="{{ $step['icon'] }}"></i>
                </div>
                <div class="text-xs font-black text-gray-200 mb-2 tracking-widest">ÉTAPE {{ $step['step'] }}</div>
                <h3 class="font-display text-xl font-bold mb-3">{{ $step['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FEATURED PROJECTS -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-primary font-semibold text-sm uppercase tracking-widest mb-2">À la une</p>
                <h2 class="font-display text-4xl font-bold">Projets populaires</h2>
            </div>
            <a href="{{ route('projects.index') }}" class="btn-outline">Voir tout <i class="fas fa-arrow-right text-xs"></i></a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredProjects as $project)
            <div class="card overflow-hidden group">
                <!-- Cover -->
                <div class="relative overflow-hidden" style="height: 200px;">
                    <img src="{{ $project->cover_image_url }}" alt="{{ $project->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 left-3">
                        <span class="badge badge-purple text-xs">{{ $project->category }}</span>
                    </div>
                    @if($project->days_left <= 7 && $project->days_left > 0)
                    <div class="absolute top-3 right-3">
                        <span class="badge badge-red text-xs animate-pulse">⏰ {{ $project->days_left }}j restants</span>
                    </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <img src="{{ $project->user->avatar_url }}" class="w-6 h-6 rounded-lg object-cover">
                        <span class="text-xs text-gray-500 font-medium">{{ $project->user->name }}</span>
                        @if($project->user->university)
                        <span class="text-xs text-gray-400">· {{ Str::limit($project->user->university, 20) }}</span>
                        @endif
                    </div>

                    <h3 class="font-display font-bold text-gray-900 mb-2 text-lg leading-tight">
                        <a href="{{ route('projects.show', $project) }}" class="hover:text-primary transition-colors">
                            {{ Str::limit($project->title, 55) }}
                        </a>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ Str::limit($project->short_description, 100) }}</p>

                    <!-- Progress -->
                    <div class="progress-bar mb-2">
                        <div class="progress-fill" style="width: {{ $project->progress_percentage }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                        <span class="font-bold text-gray-800">{{ number_format($project->amount_raised, 0) }} TND</span>
                        <span class="font-semibold text-primary">{{ $project->progress_percentage }}%</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 text-xs text-gray-400">
                            <span><i class="fas fa-users mr-1"></i>{{ $project->contributors_count }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $project->days_left }}j</span>
                            <span><i class="fas fa-eye mr-1"></i>{{ $project->views_count }}</span>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="btn-primary text-xs py-1.5 px-4">
                            Voir <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <i class="fas fa-folder-open text-4xl mb-4 block"></i>
                <p>Aucun projet publié pour le moment.</p>
                <a href="{{ route('register') }}" class="btn-primary mt-4 inline-flex">Créer le premier projet</a>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- ROLE SELECTION CTA -->
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14">
            <h2 class="font-display text-4xl font-bold mb-4">Rejoignez EduFund</h2>
            <p class="text-gray-500">Choisissez votre rôle et commencez dès aujourd'hui</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('register') }}?role=etudiant" class="role-card etudiant block no-underline">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-3xl" style="background: #6C3BEE22">🎓</div>
                <h3 class="font-display text-xl font-bold text-gray-900 mb-2">Étudiant</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Créez et financez vos projets innovants avec l'aide de notre IA</p>
                <div class="mt-4 text-primary font-semibold text-sm">Commencer →</div>
            </a>
            <a href="{{ route('register') }}?role=contributeur" class="role-card contributeur block no-underline">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-3xl" style="background: #F59E0B22">💰</div>
                <h3 class="font-display text-xl font-bold text-gray-900 mb-2">Contributeur</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Soutenez des projets étudiants prometteurs et suivez leur évolution</p>
                <div class="mt-4 text-amber-600 font-semibold text-sm">Contribuer →</div>
            </a>
            <a href="{{ route('register') }}?role=partenaire" class="role-card partenaire block no-underline">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-3xl" style="background: #10B98122">🤝</div>
                <h3 class="font-display text-xl font-bold text-gray-900 mb-2">Partenaire</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Proposez du mentorat, des stages et collaborez avec les talents de demain</p>
                <div class="mt-4 text-green-600 font-semibold text-sm">Devenir partenaire →</div>
            </a>
        </div>
    </div>
</section>

@endsection
