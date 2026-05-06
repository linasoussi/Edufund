@extends('layouts.app')
@section('title', 'Opportunités — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h1 class="font-display text-3xl font-bold text-gray-900">Opportunités 📢</h1>
        <p class="text-gray-500 mt-2">Découvrez les offres de stage, mentorat, emploi et collaboration proposées par nos partenaires</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($offers as $offer)
        <div class="card p-5 hover:shadow-lg transition-all">
            <div class="flex items-start gap-3 mb-3">
                <img src="{{ $offer->user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $offer->user->name }}</p>
                    @if($offer->user->company)
                        <p class="text-xs text-gray-500">{{ $offer->user->company }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-2">
                <span class="badge badge-purple text-xs">{{ ucfirst($offer->type) }}</span>
                @if($offer->location)
                    <span class="badge badge-amber text-xs"><i class="fas fa-map-marker-alt"></i> {{ $offer->location }}</span>
                @endif
                @if($offer->deadline)
                    <span class="badge badge-red text-xs"><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($offer->deadline)->format('d/m/Y') }}</span>
                @endif
            </div>
            <h3 class="font-display font-bold text-lg text-gray-900 mb-2">{{ $offer->title }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ Str::limit($offer->description, 120) }}</p>
            @if($offer->contact_email)
                <a href="mailto:{{ $offer->contact_email }}" class="btn-primary text-sm py-2 px-4 w-full justify-center">
                    Postuler <i class="fas fa-paper-plane"></i>
                </a>
            @else
                <p class="text-xs text-gray-400 text-center mt-2">Contactez le partenaire directement</p>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-16 text-gray-400">
            <i class="fas fa-bullhorn text-4xl mb-3 block"></i>
            <p>Aucune offre disponible pour le moment.</p>
            @auth
                @if(auth()->user()->isPartenaire())
                <button onclick="document.getElementById('offer-modal').classList.remove('hidden')" class="btn-primary mt-4">
                    Publier une offre
                </button>
                @endif
            @endauth
        </div>
        @endforelse
    </div>

    <div class="mt-8 flex justify-center">
        {{ $offers->links() }}
    </div>
</div>
@endsection