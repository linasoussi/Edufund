@extends('layouts.app')
@section('title', 'Connexion — EduFund')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4" style="background: linear-gradient(135deg, #0A0A0F, #1C1028)">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 no-underline">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg,#6C3BEE,#F59E0B)">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <span class="font-display font-bold text-3xl text-white">Edu<span class="gradient-text">Fund</span></span>
            </a>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-2xl" style="box-shadow: 0 40px 80px rgba(108,59,238,0.3)">
            <h2 class="font-display text-2xl font-bold text-gray-900 mb-1">Bon retour ! 👋</h2>
            <p class="text-gray-500 text-sm mb-8">Connectez-vous à votre espace EduFund</p>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com" class="form-input" required autofocus>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="••••••••" class="form-input pr-10" required>
                        <button type="button" onclick="togglePass()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye text-sm" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded" style="accent-color: #6C3BEE">
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full justify-center text-base py-3.5">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Créer un compte</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass() {
    const p = document.getElementById('password');
    const i = document.getElementById('eye-icon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'fas fa-eye text-sm' : 'fas fa-eye-slash text-sm';
}
</script>
@endpush
