@extends('layouts.app')
@section('title', 'Mes projets — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-gray-900">📁 Mes projets</h1>
            <p class="text-gray-500 mt-1">Gérez l’ensemble de vos campagnes</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Nouveau projet
        </a>
    </div>

    @if($projects->count() > 0)
        <div class="space-y-4">
            @foreach($projects as $project)
            <div class="card p-5 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Image -->
                    <img src="{{ $project->cover_image_url }}" alt="{{ $project->title }}" class="w-full md:w-40 h-32 object-cover rounded-xl">
                    
                    <!-- Infos -->
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h3 class="font-display font-bold text-xl text-gray-900">
                                <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->title }}</a>
                            </h3>
                            <span class="badge {{ $project->status === 'published' ? 'badge-green' : ($project->status === 'funded' ? 'badge-purple' : 'badge-amber') }}">
                                {{ ['draft' => '📝 Brouillon', 'published' => '🟢 Publié', 'funded' => '🏆 Financé', 'closed' => '🔒 Clôturé'][$project->status] ?? $project->status }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($project->short_description, 120) }}</p>
                        
                        <!-- Progression -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-gray-500">
                            <div><span class="font-semibold text-gray-800">{{ number_format($project->amount_raised) }} TND</span> / {{ number_format($project->funding_goal) }} TND</div>
                            <div><i class="fas fa-chart-line mr-1"></i> {{ $project->progress_percentage }}%</div>
                            <div><i class="fas fa-clock mr-1"></i> {{ $project->days_left }} jours restants</div>
                            <div><i class="fas fa-users mr-1"></i> {{ $project->contributors_count }} contributeurs</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-row md:flex-col gap-2 items-start">
                        <a href="{{ route('projects.edit', $project) }}" class="btn-outline text-sm py-1.5 px-3">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @if($project->status === 'draft')
                        <form action="{{ route('projects.publish', $project) }}" method="POST">
                            @csrf
                            <button class="btn-primary text-sm py-1.5 px-3">
                                <i class="fas fa-rocket"></i> Publier
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce projet ?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm py-1.5 px-3">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $projects->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-folder-open text-5xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 mb-4">Vous n’avez encore créé aucun projet.</p>
            <a href="{{ route('projects.create') }}" class="btn-primary">Créer mon premier projet</a>
        </div>
    @endif
</div>
@endsection