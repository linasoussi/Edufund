@extends('layouts.app')
@section('title', 'Modifier le projet — CoFund')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-gray-900">✏️ Modifier le projet</h1>
        <p class="text-gray-500 mt-1">Modifiez les informations de votre projet</p>
    </div>

    <div class="card p-8">
        <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Titre -->
                <div>
                    <label class="form-label">Titre du projet</label>
                    <input type="text" name="title" value="{{ old('title', $project->title) }}" class="form-input" required>
                </div>

                <!-- Résumé -->
                <div>
                    <label class="form-label">Résumé</label>
                    <textarea name="short_description" rows="3" class="form-input" required>{{ old('short_description', $project->short_description) }}</textarea>
                </div>

                <!-- Description complète -->
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="8" class="form-input" required>{{ old('description', $project->description) }}</textarea>
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="form-label">Catégorie</label>
                    <select name="category" class="form-input">
                        <option value="Technologie & Innovation" {{ $project->category == 'Technologie & Innovation' ? 'selected' : '' }}>Technologie & Innovation</option>
                        <option value="Environnement & Énergie" {{ $project->category == 'Environnement & Énergie' ? 'selected' : '' }}>Environnement & Énergie</option>
                        <option value="Santé & Bien-être" {{ $project->category == 'Santé & Bien-être' ? 'selected' : '' }}>Santé & Bien-être</option>
                        <option value="Éducation & Formation" {{ $project->category == 'Éducation & Formation' ? 'selected' : '' }}>Éducation & Formation</option>
                        <option value="Art & Culture" {{ $project->category == 'Art & Culture' ? 'selected' : '' }}>Art & Culture</option>
                        <option value="Social & Humanitaire" {{ $project->category == 'Social & Humanitaire' ? 'selected' : '' }}>Social & Humanitaire</option>
                        <option value="Agriculture & Alimentation" {{ $project->category == 'Agriculture & Alimentation' ? 'selected' : '' }}>Agriculture & Alimentation</option>
                        <option value="Autre" {{ $project->category == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <!-- Objectif et deadline -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Objectif financier (TND)</label>
                        <input type="number" name="funding_goal" value="{{ old('funding_goal', $project->funding_goal) }}" min="100" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Date limite</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" class="form-input" required>
                    </div>
                </div>

                <!-- Image de couverture -->
                <div>
                    <label class="form-label">Image de couverture</label>
                    @if($project->cover_image)
                        <div class="mb-2">
                            <img src="{{ $project->cover_image_url }}" class="w-32 h-32 object-cover rounded-xl">
                        </div>
                    @endif
                    <input type="file" name="cover_image" class="form-input">
                </div>

                <!-- Tags -->
                <div>
                    <label class="form-label">Tags (séparés par des virgules)</label>
                    <input type="text" name="tags" value="{{ old('tags', is_array($project->tags) ? implode(',', $project->tags) : '') }}" class="form-input">
                </div>
            </div>

            <div class="flex justify-between gap-3 mt-8">
                <a href="{{ route('projects.show', $project) }}" class="btn-outline">Annuler</a>
                <button type="submit" class="btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection