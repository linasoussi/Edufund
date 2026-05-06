@extends('layouts.app')
@section('title', $user->name . ' — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="card p-8 mb-8">
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-2xl object-cover">
            <div>
                <h1 class="font-display text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                @if($user->company)
                    <p class="text-primary font-semibold text-lg mt-1">{{ $user->company }}</p>
                @endif
                @if($user->bio)
                    <p class="text-gray-600 mt-3">{{ $user->bio }}</p>
                @endif
                <div class="flex gap-4 mt-4">
                    @if($user->website)
                        <a href="{{ $user->website }}" target="_blank" class="text-gray-500 hover:text-primary transition">
                            <i class="fas fa-globe"></i> Site web
                        </a>
                    @endif
                    @if($user->linkedin)
                        <a href="{{ $user->linkedin }}" target="_blank" class="text-gray-500 hover:text-primary transition">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <h2 class="font-display text-2xl font-bold mb-5">📢 Offres proposées</h2>
    @forelse($user->offers->where('is_active', true) as $offer)
    <div class="card p-5 mb-4">
        <div class="flex flex-wrap justify-between items-start gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-display font-bold text-xl text-gray-900">{{ $offer->title }}</h3>
                    <span class="badge badge-purple text-xs">{{ $offer->type }}</span>
                </div>
                <p class="text-gray-600">{{ $offer->description }}</p>
                @if($offer->location)
                    <p class="text-gray-400 text-sm mt-2"><i class="fas fa-map-marker-alt mr-1"></i>{{ $offer->location }}</p>
                @endif
                @if($offer->deadline)
                    <p class="text-gray-400 text-sm mt-1">📅 Date limite : {{ \Carbon\Carbon::parse($offer->deadline)->format('d/m/Y') }}</p>
                @endif
                @if($offer->contact_email)
                    <p class="text-gray-400 text-sm mt-1">📧 Contact : {{ $offer->contact_email }}</p>
                @endif
            </div>
            @auth
                @if(auth()->user()->isEtudiant())
                    <a href="mailto:{{ $offer->contact_email }}" class="btn-primary text-sm py-2 px-4">
                        Postuler <i class="fas fa-paper-plane"></i>
                    </a>
                @endif
            @endauth
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <i class="fas fa-bullhorn text-3xl mb-2 block"></i>
        <p>Aucune offre active pour le moment.</p>
    </div>
    @endforelse
</div>
@endsection