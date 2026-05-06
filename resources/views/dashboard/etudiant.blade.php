@extends('layouts.app')
@section('title', 'Tableau de bord — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Welcome -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-gray-900">
                Bonjour, {{ Str::limit(auth()->user()->name, 20) }} 👋
            </h1>
            <p class="text-gray-500 mt-1">Voici l'état de vos campagnes CoFund</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Nouveau projet
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['icon' => 'fas fa-folder', 'color' => '#6C3BEE', 'bg' => '#EDE9FE', 'value' => $stats['total_projects'], 'label' => 'Projets créés'],
            ['icon' => 'fas fa-rocket', 'color' => '#10B981', 'bg' => '#D1FAE5', 'value' => $stats['published_projects'], 'label' => 'Projets publiés'],
            ['icon' => 'fas fa-coins', 'color' => '#F59E0B', 'bg' => '#FEF3C7', 'value' => number_format($stats['total_raised'], 0) . ' TND', 'label' => 'Total collecté'],
            ['icon' => 'fas fa-users', 'color' => '#3B82F6', 'bg' => '#DBEAFE', 'value' => $stats['total_contributors'], 'label' => 'Contributeurs'],
        ] as $stat)
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $stat['bg'] }}">
                    <i class="{{ $stat['icon'] }}" style="color: {{ $stat['color'] }}"></i>
                </div>
            </div>
            <p class="font-display text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- My Projects -->
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display text-xl font-bold">Mes projets récents</h2>
            <a href="{{ route('projects.mine') }}" class="text-primary text-sm font-semibold hover:underline">Voir tous →</a>
        </div>

        @forelse($projects as $project)
        <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100 mb-2">
            <img src="{{ $project->cover_image_url }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-gray-900 hover:text-primary truncate">{{ $project->title }}</a>
                    <span class="badge text-xs flex-shrink-0 {{ ['draft'=>'badge-amber','published'=>'badge-green','funded'=>'badge-purple','closed'=>'badge-red'][$project->status] ?? 'badge-blue' }}">
                        {{ ['draft'=>'Brouillon','published'=>'Publié','funded'=>'Financé','closed'=>'Clôturé'][$project->status] ?? $project->status }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span>{{ number_format($project->amount_raised, 0) }} / {{ number_format($project->funding_goal, 0) }} TND</span>
                    <span>{{ $project->progress_percentage }}%</span>
                    <span>{{ $project->days_left }}j restants</span>
                </div>
                <div class="progress-bar mt-2" style="height: 5px">
                    <div class="progress-fill" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('projects.edit', $project) }}" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-purple-100 flex items-center justify-center text-gray-500 hover:text-primary transition-colors">
                    <i class="fas fa-edit text-xs"></i>
                </a>
                <a href="{{ route('projects.show', $project) }}" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-purple-100 flex items-center justify-center text-gray-500 hover:text-primary transition-colors">
                    <i class="fas fa-eye text-xs"></i>
                </a>
                @if($project->status === 'draft')
                <form action="{{ route('projects.publish', $project) }}" method="POST">
                    @csrf
                    <button class="w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 flex items-center justify-center text-green-600 transition-colors" title="Publier">
                        <i class="fas fa-rocket text-xs"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-folder-open text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-gray-500 mb-4">Vous n'avez pas encore de projet</p>
            <a href="{{ route('projects.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Créer mon premier projet
            </a>
        </div>
        @endforelse
    </div>

    <!-- ==================== PROPOSITIONS DE PARTENARIAT ==================== -->
    @if(isset($partnershipRequests) && $partnershipRequests->count() > 0)
    <div class="card p-6 mb-6">
        <h2 class="font-display text-xl font-bold mb-5">🤝 Propositions de partenariat</h2>
        @foreach($partnershipRequests as $p)
        <div class="border border-gray-100 rounded-xl p-4 mb-4 hover:shadow-sm transition-shadow">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-semibold text-gray-900">{{ $p->title }}</span>
                        <span class="badge badge-purple text-xs">{{ $p->type }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $p->description }}</p>
                    <p class="text-xs text-gray-400">
                        Proposé par <strong>{{ $p->partner->name ?? 'Partenaire' }}</strong>
                        @if($p->project) · <a href="{{ route('projects.show', $p->project) }}" class="text-primary hover:underline">Voir le projet</a> @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    @if($p->status === 'pending')
                    <form action="{{ route('partnerships.respond', $p) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="accepted">
                        <button class="btn-primary text-sm py-1.5 px-4">
                            ✅ Accepter
                        </button>
                    </form>
                    <form action="{{ route('partnerships.respond', $p) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="refused">
                        <button class="btn-outline text-sm py-1.5 px-4">
                            ❌ Refuser
                        </button>
                    </form>
                    @else
                    <span class="badge {{ $p->status === 'accepted' ? 'badge-green' : 'badge-red' }} text-xs">
                        {{ $p->status === 'accepted' ? 'Accepté' : 'Refusé' }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Notifications -->
    @if($notifications->count() > 0)
    <div class="card p-6">
        <h2 class="font-display text-xl font-bold mb-5">🔔 Notifications récentes</h2>
        @foreach($notifications as $notif)
        <div class="flex items-start gap-3 p-3 rounded-xl mb-2 {{ !$notif->is_read ? 'bg-purple-50 border border-purple-100' : 'hover:bg-gray-50' }} transition-colors">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ !$notif->is_read ? 'bg-purple-100' : 'bg-gray-100' }}">
                <i class="fas {{ ['contribution'=>'fa-coins text-amber-500','partnership'=>'fa-handshake text-green-500','partnership_response'=>'fa-check text-blue-500'][$notif->type] ?? 'fa-bell text-purple-500' }} text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</p>
                <p class="text-gray-500 text-xs mt-0.5">{{ $notif->message }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notif->is_read)
            <span class="w-2 h-2 bg-primary rounded-full flex-shrink-0 mt-1.5"></span>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection