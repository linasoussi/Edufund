@extends('layouts.app')
@section('title', 'Explorer les projets — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold text-gray-900">Explorer les projets 🔍</h1>
            <p class="text-gray-500 mt-1">{{ $projects->total() }} projets publiés par des étudiants</p>
        </div>
        @auth
        @if(auth()->user()->isEtudiant())
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Créer mon projet
        </a>
        @endif
        @endauth
    </div>

    <!-- Filters -->
    <div class="card p-5 mb-8">
        <form action="{{ route('projects.search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un projet..."
                    class="form-input pl-10">
            </div>

            <!-- Category -->
            <select name="category" class="form-input md:w-52">
                <option value="">Toutes les catégories</option>
                @foreach(['Technologie & Innovation', 'Environnement & Énergie', 'Santé & Bien-être', 'Éducation & Formation', 'Art & Culture', 'Social & Humanitaire', 'Agriculture & Alimentation', 'Autre'] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <!-- Sort -->
            <select name="sort" class="form-input md:w-44">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Plus récents</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Plus populaires</option>
                <option value="funded" {{ request('sort') === 'funded' ? 'selected' : '' }}>Plus financés</option>
                <option value="ending" {{ request('sort') === 'ending' ? 'selected' : '' }}>Se terminent bientôt</option>
            </select>

            <button type="submit" class="btn-primary px-6">
                <i class="fas fa-filter text-xs"></i> Filtrer
            </button>

            @if(request()->hasAny(['q','category','sort']))
            <a href="{{ route('projects.index') }}" class="btn-outline px-4 text-sm">
                <i class="fas fa-times text-xs"></i> Réinitialiser
            </a>
            @endif
        </form>
    </div>

    <!-- Category Quick Filters -->
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('projects.index') }}" class="category-tag {{ !request('category') ? 'active' : '' }}">Tous</a>
        @foreach(['Technologie & Innovation', 'Environnement & Énergie', 'Santé & Bien-être', 'Éducation & Formation', 'Art & Culture', 'Social & Humanitaire'] as $cat)
        <a href="{{ route('projects.search', ['category' => $cat]) }}" class="category-tag {{ request('category') === $cat ? 'active' : '' }}">{{ $cat }}</a>
        @endforeach
    </div>

    <!-- Projects Grid -->
    @if($projects->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @foreach($projects as $project)
        <div class="card overflow-hidden group flex flex-col">
            <!-- Cover -->
            <div class="relative overflow-hidden flex-shrink-0" style="height: 210px">
                <img src="{{ $project->cover_image_url }}" alt="{{ $project->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <!-- Badges overlay -->
                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                    <span class="badge badge-purple text-xs">{{ $project->category }}</span>
                    @if($project->success_score)
                    <span class="badge text-xs" style="background: rgba(10,10,15,0.8); color: #F59E0B; border: 1px solid #F59E0B44">
                        <i class="fas fa-robot text-xs"></i> {{ $project->success_score }}%
                    </span>
                    @endif
                </div>

                @if($project->days_left <= 7 && $project->days_left > 0)
                <div class="absolute top-3 right-3">
                    <span class="badge badge-red text-xs animate-pulse">⏰ {{ $project->days_left }}j</span>
                </div>
                @elseif($project->status === 'funded')
                <div class="absolute top-3 right-3">
                    <span class="badge badge-green text-xs">🏆 Financé</span>
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="p-5 flex flex-col flex-1">
                <!-- Creator -->
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ $project->user->avatar_url }}" class="w-6 h-6 rounded-lg object-cover">
                    <span class="text-xs text-gray-500 font-medium">{{ $project->user->name }}</span>
                    @if($project->user->university)
                    <span class="text-xs text-gray-400 truncate">· {{ Str::limit($project->user->university, 18) }}</span>
                    @endif
                </div>

                <h3 class="font-display font-bold text-gray-900 mb-2 text-lg leading-tight">
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-primary transition-colors">
                        {{ Str::limit($project->title, 55) }}
                    </a>
                </h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1">{{ Str::limit($project->short_description, 95) }}</p>

                <!-- Progress -->
                <div class="progress-bar mb-2">
                    <div class="progress-fill" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mb-4">
                    <span class="font-bold text-gray-800">{{ number_format($project->amount_raised, 0) }} TND</span>
                    <span>/ {{ number_format($project->funding_goal, 0) }} TND</span>
                    <span class="font-semibold text-primary">{{ $project->progress_percentage }}%</span>
                </div>

                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span title="Contributeurs"><i class="fas fa-users mr-1 text-purple-400"></i>{{ $project->contributors_count }}</span>
                        <span title="Jours restants"><i class="fas fa-clock mr-1 text-amber-400"></i>{{ $project->days_left }}j</span>
                        <span title="Vues"><i class="fas fa-eye mr-1 text-blue-400"></i>{{ number_format($project->views_count) }}</span>
                    </div>
                    <a href="{{ route('projects.show', $project) }}" class="btn-primary text-xs py-1.5 px-4">
                        Voir <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $projects->links() }}
    </div>

    @else
    <div class="text-center py-20">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
            <i class="fas fa-search text-3xl text-gray-300"></i>
        </div>
        <h3 class="font-display text-xl font-bold text-gray-700 mb-2">Aucun projet trouvé</h3>
        <p class="text-gray-400 mb-6">Essayez d'autres termes de recherche ou explorez toutes les catégories</p>
        <a href="{{ route('projects.index') }}" class="btn-primary">Voir tous les projets</a>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.category-tag { padding: 0.4rem 1rem; border-radius: 50px; border: 2px solid #E5E7EB; font-size: 0.8rem; font-weight: 600; color: #6B7280; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
.category-tag:hover, .category-tag.active { border-color: #6C3BEE; background: #EDE9FE; color: #6C3BEE; }
</style>
@endpush
