{{-- ============================================ --}}
{{-- dashboard/contributeur.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Mon espace Contributeur — CoFund')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold">Bonjour, {{ Str::limit(auth()->user()->name, 20) }} 💰</h1>
            <p class="text-gray-500 mt-1">Votre impact sur CoFund</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn-primary">
            <i class="fas fa-search"></i> Explorer les projets
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['icon'=>'fas fa-hand-holding-heart','color'=>'#EF4444','bg'=>'#FEE2E2','value'=>$stats['total_contributions'],'label'=>'Contributions'],
            ['icon'=>'fas fa-coins','color'=>'#F59E0B','bg'=>'#FEF3C7','value'=>number_format($stats['total_amount'],0).' TND','label'=>'Total donné'],
            ['icon'=>'fas fa-bookmark','color'=>'#6C3BEE','bg'=>'#EDE9FE','value'=>$stats['followed_projects'],'label'=>'Projets suivis'],
            ['icon'=>'fas fa-trophy','color'=>'#10B981','bg'=>'#D1FAE5','value'=>$stats['funded_projects'],'label'=>'Projets financés'],
        ] as $s)
        <div class="card p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:{{ $s['bg'] }}">
                <i class="{{ $s['icon'] }}" style="color:{{ $s['color'] }}"></i>
            </div>
            <p class="font-display text-2xl font-bold">{{ $s['value'] }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Contributions -->
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold mb-5">Mes contributions récentes</h2>
            @forelse($recentContributions as $c)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 mb-2 transition-colors">
                <img src="{{ $c->project->cover_image_url }}" class="w-12 h-12 rounded-xl object-cover">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('projects.show', $c->project) }}" class="font-semibold text-gray-900 text-sm hover:text-primary truncate block">{{ $c->project->title }}</a>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="progress-bar flex-1" style="height: 4px">
                            <div class="progress-fill" style="width: {{ $c->project->progress_percentage }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $c->project->progress_percentage }}%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                </div>
                <span class="font-display font-bold text-primary flex-shrink-0">{{ number_format($c->amount, 0) }} TND</span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fas fa-hand-holding-heart text-3xl mb-2 block text-gray-200"></i>
                <p>Aucune contribution pour l'instant</p>
                <a href="{{ route('projects.index') }}" class="btn-primary mt-3 text-xs py-2 px-4">Explorer les projets</a>
            </div>
            @endforelse
        </div>

        <!-- Followed Projects -->
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold mb-5">Projets suivis</h2>
            @forelse($followedProjects as $project)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 mb-2 transition-colors">
                <img src="{{ $project->cover_image_url }}" class="w-12 h-12 rounded-xl object-cover">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-gray-900 text-sm hover:text-primary truncate block">{{ $project->title }}</a>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $project->days_left }}j restants · {{ $project->progress_percentage }}% financé</p>
                </div>
                <span class="badge {{ $project->status === 'funded' ? 'badge-green' : 'badge-purple' }} text-xs">{{ $project->status === 'funded' ? '🏆 Financé' : '🟢 Actif' }}</span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fas fa-bookmark text-3xl mb-2 block text-gray-200"></i>
                <p>Aucun projet suivi</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
