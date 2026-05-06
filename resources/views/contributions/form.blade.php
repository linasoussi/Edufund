@extends('layouts.app')
@section('title', 'Contribuer — CoFund')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <h1 class="font-display text-3xl font-bold text-gray-900 mb-2">Soutenir ce projet 💰</h1>
        <p class="text-gray-500">Votre contribution fait la différence</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Project Info -->
        <div class="card p-5">
            <img src="{{ $project->cover_image_url }}" class="w-full h-40 object-cover rounded-xl mb-4">
            <h3 class="font-display font-bold text-gray-900 mb-1">{{ Str::limit($project->title, 50) }}</h3>
            <p class="text-gray-500 text-xs mb-3">par {{ $project->user->name }}</p>
            <div class="progress-bar mb-2">
                <div class="progress-fill" style="width: {{ $project->progress_percentage }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                <span class="font-bold text-gray-800">{{ number_format($project->amount_raised, 0) }} TND</span>
                <span>{{ $project->progress_percentage }}% de {{ number_format($project->funding_goal, 0) }} TND</span>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-center">
                <div class="bg-gray-50 rounded-xl p-2">
                    <p class="font-bold text-gray-900 text-sm">{{ $project->contributors_count }}</p>
                    <p class="text-xs text-gray-400">contributeurs</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-2">
                    <p class="font-bold text-gray-900 text-sm">{{ $project->days_left }}</p>
                    <p class="text-xs text-gray-400">jours restants</p>
                </div>
            </div>
        </div>

        <!-- Contribution Form -->
        <div class="card p-6">
            <form action="{{ route('contributions.store', $project) }}" method="POST" class="space-y-5">
                @csrf

                <!-- Amount -->
                <div>
                    <label class="form-label">Montant de votre contribution</label>
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        @foreach([10, 25, 50, 100] as $amount)
                        <button type="button" onclick="setAmount({{ $amount }})"
                            class="py-2 rounded-xl border-2 border-gray-200 text-sm font-bold text-gray-600 hover:border-primary hover:text-primary hover:bg-purple-50 transition-all preset-btn">
                            {{ $amount }} TND
                        </button>
                        @endforeach
                    </div>
                    <div class="relative">
                        <input type="number" name="amount" id="amount-input" value="{{ old('amount', 25) }}"
                            min="1" step="1" placeholder="Autre montant..." class="form-input pr-14"
                            oninput="clearPresets()">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">TND</span>
                    </div>
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="form-label">Mode de paiement</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            ['carte_bancaire', 'fas fa-credit-card', 'Carte bancaire'],
                            ['paypal', 'fab fa-paypal', 'PayPal'],
                            ['virement', 'fas fa-university', 'Virement'],
                            ['mobile_money', 'fas fa-mobile-alt', 'Mobile Money'],
                        ] as [$val, $icon, $label])
                        <label class="flex items-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-primary hover:bg-purple-50 transition-all has-[:checked]:border-primary has-[:checked]:bg-purple-50">
                            <input type="radio" name="payment_method" value="{{ $val }}" class="sr-only" {{ old('payment_method', 'carte_bancaire') === $val ? 'checked' : '' }}>
                            <i class="{{ $icon }} text-primary text-sm w-4 text-center"></i>
                            <span class="text-xs font-semibold text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Message -->
                <div>
                    <label class="form-label">Message <span class="text-gray-400 font-normal">(optionnel)</span></label>
                    <textarea name="message" rows="2" placeholder="Encouragez le porteur du projet..." class="form-input resize-none text-sm"></textarea>
                </div>

                <!-- Anonymous -->
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" class="w-4 h-4 rounded" style="accent-color: #6C3BEE" {{ old('is_anonymous') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-600">Contribuer de manière anonyme</span>
                </label>

                <button type="submit" class="btn-primary w-full justify-center text-base py-3.5">
                    <i class="fas fa-heart"></i>
                    Confirmer ma contribution
                </button>

                <p class="text-xs text-gray-400 text-center">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                    Transaction sécurisée · CoFund ne stocke pas vos données bancaires
                </p>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setAmount(val) {
    document.getElementById('amount-input').value = val;
    document.querySelectorAll('.preset-btn').forEach(b => {
        b.classList.remove('border-primary', 'text-primary', 'bg-purple-50');
        b.classList.add('border-gray-200', 'text-gray-600');
    });
    event.target.classList.add('border-primary', 'text-primary', 'bg-purple-50');
}
function clearPresets() {
    document.querySelectorAll('.preset-btn').forEach(b => {
        b.classList.remove('border-primary', 'text-primary', 'bg-purple-50');
        b.classList.add('border-gray-200', 'text-gray-600');
    });
}
// Init first preset
document.querySelector('.preset-btn:nth-child(2)')?.classList.add('border-primary','text-primary','bg-purple-50');
</script>
@endpush
