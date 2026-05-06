@extends('layouts.app')
@section('title', 'Nos partenaires — CoFund')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h1 class="font-display text-3xl font-bold text-gray-900">Nos partenaires 🤝</h1>
        <p class="text-gray-500 mt-2">Découvrez les entreprises et organisations qui soutiennent les étudiants</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($partners as $partner)
        <div class="card p-6 hover:shadow-xl transition-all">
            <div class="flex items-start gap-4">
                <img src="{{ $partner->avatar_url }}" alt="{{ $partner->name }}" class="w-16 h-16 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-display font-bold text-lg text-gray-900">{{ $partner->name }}</h3>
                    @if($partner->company)
                        <p class="text-primary font-medium text-sm">{{ $partner->company }}</p>
                    @endif
                    <p class="text-gray-500 text-sm mt-1">{{ Str::limit($partner->bio ?? 'Partenaire engagé pour l\'innovation étudiante', 80) }}</p>
                    <a href="{{ route('partners.show', $partner) }}" class="inline-block mt-3 text-primary text-sm font-semibold hover:underline">
                        Voir ses offres →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 text-gray-400">
            <i class="fas fa-handshake text-4xl mb-3 block"></i>
            <p>Aucun partenaire pour le moment.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8 flex justify-center">
        {{ $partners->links() }}
    </div>
</div>
@endsection