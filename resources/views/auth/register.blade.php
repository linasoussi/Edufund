@extends('layouts.app')
@section('title', 'Inscription — EduFund')

@push('styles')
<style>
.register-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #0A0A0F 0%, #1C1028 60%, #0D1117 100%);
    display: flex;
    align-items: center;
    padding: 2rem 0;
}
.register-card {
    background: white;
    border-radius: 32px;
    overflow: hidden;
    box-shadow: 0 40px 100px rgba(108,59,238,0.3);
    max-width: 1000px;
    width: 100%;
    margin: 0 auto;
}
.role-select-btn {
    border: 2px solid #E5E7EB;
    border-radius: 20px;
    padding: 1.5rem 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    text-align: center;
    background: white;
    width: 100%;
    position: relative;
    overflow: hidden;
}
.role-select-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.3s;
}
.role-select-btn:hover { transform: translateY(-4px); }
.role-select-btn.etudiant:hover, .role-select-btn.etudiant.selected {
    border-color: #6C3BEE;
    background: linear-gradient(135deg, #EDE9FE, #F5F3FF);
    box-shadow: 0 8px 25px rgba(108,59,238,0.2);
}
.role-select-btn.contributeur:hover, .role-select-btn.contributeur.selected {
    border-color: #F59E0B;
    background: linear-gradient(135deg, #FEF3C7, #FFFBEB);
    box-shadow: 0 8px 25px rgba(245,158,11,0.2);
}
.role-select-btn.partenaire:hover, .role-select-btn.partenaire.selected {
    border-color: #10B981;
    background: linear-gradient(135deg, #D1FAE5, #ECFDF5);
    box-shadow: 0 8px 25px rgba(16,185,129,0.2);
}
.role-select-btn .check-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #6C3BEE;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    opacity: 0;
    transition: opacity 0.2s;
}
.role-select-btn.selected .check-icon { opacity: 1; }
.role-select-btn.contributeur.selected .check-icon { background: #F59E0B; }
.role-select-btn.partenaire.selected .check-icon { background: #10B981; }

.extra-fields { display: none; }
.extra-fields.show { display: block; animation: fadeInUp 0.4s ease; }

.step-indicator {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 2rem;
}
.step {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.3s;
}
.step.active { background: #6C3BEE; color: white; }
.step.done { background: #10B981; color: white; }
.step.inactive { background: #E5E7EB; color: #9CA3AF; }
.step-line { flex: 1; height: 2px; background: #E5E7EB; transition: background 0.3s; }
.step-line.done { background: #10B981; }
</style>
@endpush

@section('content')
<div class="register-bg">
    <div class="w-full px-4">
        <div class="register-card">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                <!-- Left Panel -->
                <div class="p-10 lg:p-12" style="background: linear-gradient(135deg, #6C3BEE, #4C1D95)">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-12 no-underline">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <span class="font-display font-bold text-2xl text-white">EduFund</span>
                    </a>

                    <h2 class="font-display text-3xl font-bold text-white mb-4">Rejoignez notre<br>communauté 🚀</h2>
                    <p class="text-purple-200 leading-relaxed mb-10">Plus de {{ number_format(rand(500,900)) }} étudiants, contributeurs et partenaires font confiance à EduFund.</p>

                    <div class="space-y-5">
                        @foreach([
                            ['icon' => '🤖', 'title' => 'Assistant IA intégré', 'desc' => 'Créez votre projet avec l\'aide de notre IA'],
                            ['icon' => '📊', 'title' => 'Prédiction de succès', 'desc' => 'Analysez vos chances avant de publier'],
                            ['icon' => '🤝', 'title' => 'Réseau de partenaires', 'desc' => 'Accédez à des opportunités exclusives'],
                            ['icon' => '💰', 'title' => 'Paiement sécurisé', 'desc' => 'Transactions protégées et transparentes'],
                        ] as $feat)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-xl flex-shrink-0">{{ $feat['icon'] }}</div>
                            <div>
                                <p class="font-semibold text-white text-sm">{{ $feat['title'] }}</p>
                                <p class="text-purple-300 text-xs mt-0.5">{{ $feat['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Panel: Form -->
                <div class="p-10 lg:p-12 overflow-y-auto" style="max-height: 90vh;">
                    <div class="mb-8">
                        <h3 class="font-display text-2xl font-bold text-gray-900 mb-1">Créer un compte</h3>
                        <p class="text-gray-500 text-sm">Déjà membre ? <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Se connecter</a></p>
                    </div>

                    <form action="{{ route('register') }}" method="POST" id="register-form">
                        @csrf

                        <!-- STEP 1: Role Selection -->
                        <div id="step-1">
                            <p class="font-display font-bold text-gray-800 mb-4">Je suis...</p>
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <!-- Étudiant -->
                                <button type="button" onclick="selectRole('etudiant')"
                                    class="role-select-btn etudiant {{ old('role') === 'etudiant' || request('role') === 'etudiant' ? 'selected' : '' }}"
                                    id="role-btn-etudiant">
                                    <div class="check-icon"><i class="fas fa-check"></i></div>
                                    <div class="text-3xl mb-2">🎓</div>
                                    <div class="font-display font-bold text-gray-900 text-sm">Étudiant</div>
                                    <div class="text-gray-500 text-xs mt-1">Porteur de projet</div>
                                </button>

                                <!-- Contributeur -->
                                <button type="button" onclick="selectRole('contributeur')"
                                    class="role-select-btn contributeur {{ old('role') === 'contributeur' || request('role') === 'contributeur' ? 'selected' : '' }}"
                                    id="role-btn-contributeur">
                                    <div class="check-icon"><i class="fas fa-check"></i></div>
                                    <div class="text-3xl mb-2">💰</div>
                                    <div class="font-display font-bold text-gray-900 text-sm">Contributeur</div>
                                    <div class="text-gray-500 text-xs mt-1">Financeur de projets</div>
                                </button>

                                <!-- Partenaire -->
                                <button type="button" onclick="selectRole('partenaire')"
                                    class="role-select-btn partenaire {{ old('role') === 'partenaire' || request('role') === 'partenaire' ? 'selected' : '' }}"
                                    id="role-btn-partenaire">
                                    <div class="check-icon"><i class="fas fa-check"></i></div>
                                    <div class="text-3xl mb-2">🤝</div>
                                    <div class="font-display font-bold text-gray-900 text-sm">Partenaire</div>
                                    <div class="text-gray-500 text-xs mt-1">Mentor / Entreprise</div>
                                </button>
                            </div>
                            <input type="hidden" name="role" id="role-input" value="{{ old('role', request('role')) }}" required>
                            @error('role')<p class="text-red-500 text-xs mb-3">{{ $message }}</p>@enderror
                        </div>

                        <!-- Common Fields -->
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Nom complet</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Votre nom et prénom" class="form-input" required>
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="form-label">Adresse email</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com" class="form-input" required>
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="form-label">Téléphone <span class="text-gray-400 font-normal">(optionnel)</span></label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+216 XX XXX XXX" class="form-input">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Mot de passe</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" placeholder="••••••••" class="form-input pr-10" required>
                                        <button type="button" onclick="togglePass('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye text-sm" id="eye-password"></i>
                                        </button>
                                    </div>
                                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="form-label">Confirmer</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirm" placeholder="••••••••" class="form-input pr-10" required>
                                        <button type="button" onclick="togglePass('password_confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-eye text-sm" id="eye-password_confirm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Étudiant Extra Fields -->
                            <div class="extra-fields {{ old('role') === 'etudiant' || request('role') === 'etudiant' ? 'show' : '' }}" id="extra-etudiant">
                                <div class="p-4 rounded-2xl mb-0" style="background: linear-gradient(135deg, #EDE9FE22, #DDD6FE22); border: 1px solid #DDD6FE">
                                    <p class="text-xs font-bold text-purple-600 mb-3 uppercase tracking-wide">📚 Informations académiques</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="form-label text-xs">Université / École</label>
                                            <input type="text" name="university" value="{{ old('university') }}" placeholder="ex: Université de Tunis" class="form-input text-sm">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Domaine d'études</label>
                                            <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" placeholder="ex: Informatique, Génie civil..." class="form-input text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Partenaire Extra Fields -->
                            <div class="extra-fields {{ old('role') === 'partenaire' || request('role') === 'partenaire' ? 'show' : '' }}" id="extra-partenaire">
                                <div class="p-4 rounded-2xl" style="background: linear-gradient(135deg, #D1FAE522, #A7F3D022); border: 1px solid #A7F3D0">
                                    <p class="text-xs font-bold text-green-600 mb-3 uppercase tracking-wide">🏢 Informations professionnelles</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="form-label text-xs">Entreprise / Organisation</label>
                                            <input type="text" name="company" value="{{ old('company') }}" placeholder="Nom de votre entreprise" class="form-input text-sm">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Site web</label>
                                            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://votre-site.com" class="form-input text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary w-full justify-center mt-6 text-base py-3.5">
                            <i class="fas fa-rocket"></i>
                            Créer mon compte gratuitement
                        </button>

                        <p class="text-xs text-gray-400 text-center mt-4">
                            En vous inscrivant, vous acceptez nos <a href="#" class="text-primary hover:underline">conditions d'utilisation</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectRole(role) {
    document.querySelectorAll('.role-select-btn').forEach(btn => btn.classList.remove('selected'));
    document.getElementById('role-btn-' + role).classList.add('selected');
    document.getElementById('role-input').value = role;

    // Show/hide extra fields
    document.querySelectorAll('.extra-fields').forEach(el => el.classList.remove('show'));
    const extra = document.getElementById('extra-' + role);
    if (extra) extra.classList.add('show');
}

function togglePass(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById('eye-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}

// Init with old value
const oldRole = document.getElementById('role-input').value;
if (oldRole) selectRole(oldRole);
</script>
@endpush
