{{-- ============================================ --}}
{{-- dashboard/partenaire.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')
@section('title', 'Mon espace Partenaire — CoFund')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold">Bonjour, {{ Str::limit(auth()->user()->name, 20) }} 🤝</h1>
            <p class="text-gray-500 mt-1">Votre espace partenaire CoFund</p>
        </div>
        <button onclick="document.getElementById('offer-modal').classList.remove('hidden')" class="btn-primary">
            <i class="fas fa-plus"></i> Publier une offre
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['icon'=>'fas fa-bullhorn','color'=>'#6C3BEE','bg'=>'#EDE9FE','value'=>$stats['total_offers'],'label'=>'Offres publiées'],
            ['icon'=>'fas fa-check-circle','color'=>'#10B981','bg'=>'#D1FAE5','value'=>$stats['active_offers'],'label'=>'Offres actives'],
            ['icon'=>'fas fa-handshake','color'=>'#F59E0B','bg'=>'#FEF3C7','value'=>$stats['partnerships'],'label'=>'Partenariats proposés'],
            ['icon'=>'fas fa-star','color'=>'#3B82F6','bg'=>'#DBEAFE','value'=>$stats['accepted_partnerships'],'label'=>'Acceptés'],
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
        <!-- My Offers -->
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold mb-5">Mes offres publiées</h2>
            @forelse($offers as $offer)
            <div class="p-4 rounded-2xl border border-gray-100 hover:bg-gray-50 mb-3 transition-colors">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-900 text-sm">{{ $offer->title }}</span>
                            <span class="badge badge-{{ ['stage'=>'blue','mentorat'=>'purple','emploi'=>'green','collaboration'=>'amber','financement'=>'red'][$offer->type] ?? 'blue' }} text-xs">{{ $offer->type }}</span>
                        </div>
                        <p class="text-xs text-gray-400">{{ Str::limit($offer->description, 70) }}</p>
                        @if($offer->location)
                        <p class="text-xs text-gray-400 mt-1"><i class="fas fa-map-marker-alt mr-1 text-red-400"></i>{{ $offer->location }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $offer->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                        <form action="{{ route('offers.toggle', $offer) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-xs text-gray-400 hover:text-primary">
                                {{ $offer->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        <form action="{{ route('offers.destroy', $offer) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fas fa-bullhorn text-3xl mb-2 block text-gray-200"></i>
                <p>Aucune offre publiée</p>
                <button onclick="document.getElementById('offer-modal').classList.remove('hidden')" class="btn-primary mt-3 text-xs py-2 px-4">
                    Publier une offre
                </button>
            </div>
            @endforelse
        </div>

        <!-- My Partnerships -->
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold mb-5">Mes partenariats</h2>
            @forelse($partnerships as $p)
            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 mb-2 transition-colors">
                <img src="{{ $p->student->avatar_url }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm">{{ $p->title }}</p>
                    <p class="text-xs text-gray-400">avec {{ $p->student->name }}</p>
                    @if($p->project)
                    <p class="text-xs text-purple-500 mt-0.5">📁 {{ Str::limit($p->project->title, 30) }}</p>
                    @endif
                </div>
                <span class="badge text-xs {{ ['pending'=>'badge-amber','accepted'=>'badge-green','refused'=>'badge-red','completed'=>'badge-blue'][$p->status] ?? 'badge-amber' }}">
                    {{ ['pending'=>'En attente','accepted'=>'Accepté','refused'=>'Refusé','completed'=>'Complété'][$p->status] ?? $p->status }}
                </span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fas fa-handshake text-3xl mb-2 block text-gray-200"></i>
                <p>Aucun partenariat proposé</p>
                <a href="{{ route('projects.index') }}" class="btn-outline mt-3 text-xs py-2 px-4">Voir les projets</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Offer Modal -->
<div id="offer-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6)">
    <div class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display text-xl font-bold">📢 Publier une offre</h3>
            <button onclick="document.getElementById('offer-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('offers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Type d'offre</label>
                    <select name="type" class="form-input">
                        <option value="stage">💼 Stage</option>
                        <option value="mentorat">🧑‍🏫 Mentorat</option>
                        <option value="emploi">💼 Emploi</option>
                        <option value="collaboration">🤝 Collaboration</option>
                        <option value="financement">💰 Financement</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Localisation</label>
                    <input type="text" name="location" placeholder="ex: Tunis, Remote" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Titre de l'offre</label>
                <input type="text" name="title" placeholder="ex: Stage développement mobile 3 mois" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" placeholder="Décrivez votre offre en détail..." class="form-input resize-none" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Date limite</label>
                    <input type="date" name="deadline" class="form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div>
                    <label class="form-label">Email de contact</label>
                    <input type="email" name="contact_email" placeholder="contact@entreprise.com" class="form-input">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full justify-center py-3">
                <i class="fas fa-bullhorn"></i> Publier l'offre
            </button>
        </form>
    </div>
</div>
@endsection
