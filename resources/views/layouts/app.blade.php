<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduFund') — Crowdfunding Étudiant</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Satoshi:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CDN (for dev - replace with Vite in prod) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#6C3BEE', light: '#8B5CF6', dark: '#4C1D95' },
                        accent: { DEFAULT: '#F59E0B', light: '#FCD34D' },
                        dark: { DEFAULT: '#0A0A0F', 800: '#12121A', 700: '#1C1C28' },
                    },
                    fontFamily: {
                        display: ['"Clash Display"', 'sans-serif'],
                        body: ['Satoshi', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Satoshi', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Clash Display', sans-serif; }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(108, 59, 238, 0.15);
        }

        .gradient-text {
            background: linear-gradient(135deg, #6C3BEE, #F59E0B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6C3BEE, #8B5CF6);
            color: white;
            padding: 0.65rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 59, 238, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: #6C3BEE;
            padding: 0.65rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #6C3BEE;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-outline:hover {
            background: #6C3BEE;
            color: white;
            transform: translateY(-2px);
        }

        .btn-accent {
            background: linear-gradient(135deg, #F59E0B, #FCD34D);
            color: #0A0A0F;
            padding: 0.65rem 1.75rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-accent:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(245,158,11,0.4); }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(108, 59, 238, 0.08);
            border: 1px solid rgba(108, 59, 238, 0.08);
            transition: all 0.3s ease;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(108,59,238,0.15); }

        .progress-bar {
            height: 8px;
            background: #E5E7EB;
            border-radius: 50px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6C3BEE, #F59E0B);
            border-radius: 50px;
            transition: width 1s ease;
        }

        /* Navbar */
        nav.main-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(108,59,238,0.1);
            box-shadow: 0 2px 20px rgba(108,59,238,0.06);
        }

        /* Alert styles */
        .alert-success {
            background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
            border: 1px solid #10B981;
            color: #065F46;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-error {
            background: linear-gradient(135deg, #FEE2E2, #FECACA);
            border: 1px solid #EF4444;
            color: #7F1D1D;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        /* Form controls */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: white;
            color: #1F2937;
            outline: none;
        }
        .form-input:focus {
            border-color: #6C3BEE;
            box-shadow: 0 0 0 3px rgba(108,59,238,0.1);
        }
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.4rem;
            display: block;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-purple { background: #EDE9FE; color: #6C3BEE; }
        .badge-amber  { background: #FEF3C7; color: #D97706; }
        .badge-green  { background: #D1FAE5; color: #059669; }
        .badge-red    { background: #FEE2E2; color: #DC2626; }
        .badge-blue   { background: #DBEAFE; color: #2563EB; }

        /* Sidebar */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: #6B7280;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(135deg, #EDE9FE, #DDD6FE);
            color: #6C3BEE;
        }
        .sidebar-link i { width: 20px; text-align: center; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.5s ease forwards; }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(108, 59, 238, 0.3); }
            50% { box-shadow: 0 0 0 8px rgba(108, 59, 238, 0); }
        }
        .pulse-glow { animation: pulse-glow 2s infinite; }

        /* Notification dot */
        .notif-dot {
            width: 8px; height: 8px;
            background: #EF4444;
            border-radius: 50%;
            position: absolute;
            top: -2px; right: -2px;
        }

        /* AI Chat */
        #ai-chat-window {
            position: fixed;
            bottom: 100px;
            right: 24px;
            width: 380px;
            max-height: 520px;
            z-index: 9999;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(108,59,238,0.25);
            display: none;
        }
        #ai-chat-window.open { display: flex; flex-direction: column; }

        .chat-msg-user { 
            align-self: flex-end;
            background: linear-gradient(135deg, #6C3BEE, #8B5CF6);
            color: white;
            border-radius: 18px 18px 4px 18px;
            padding: 0.65rem 1rem;
            max-width: 80%;
            font-size: 0.875rem;
        }
        .chat-msg-ai {
            align-self: flex-start;
            background: #F3F4F6;
            color: #1F2937;
            border-radius: 18px 18px 18px 4px;
            padding: 0.65rem 1rem;
            max-width: 85%;
            font-size: 0.875rem;
            white-space: pre-wrap;
        }

        /* Scroll custom */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F3F4F6; }
        ::-webkit-scrollbar-thumb { background: #6C3BEE; border-radius: 3px; }

        /* Toast */
        #toast-container {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Navbar -->
<nav class="main-nav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg,#6C3BEE,#F59E0B)">
                    <i class="fas fa-graduation-cap text-white text-sm"></i>
                </div>
                <span class="font-display font-bold text-xl text-gray-900">Edu<span class="gradient-text">Fund</span></span>
            </a>

            <!-- Nav links -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-primary font-medium text-sm transition-colors">Projets</a>
                <a href="{{ route('partners.index') }}" class="text-gray-600 hover:text-primary font-medium text-sm transition-colors">Partenaires</a>
                <a href="{{ route('offers.index') }}" class="text-gray-600 hover:text-primary font-medium text-sm transition-colors">Opportunités</a>
                @auth
                    @if(auth()->user()->isEtudiant())
                        <a href="{{ route('projects.create') }}" class="text-gray-600 hover:text-primary font-medium text-sm transition-colors">Créer un projet</a>
                    @endif
                @endauth
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn-outline text-sm py-2 px-4">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-4">Inscription</a>
                @else
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notif-btn" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center hover:bg-purple-50 transition-colors relative">
                            <i class="fas fa-bell text-gray-600 text-sm"></i>
                            @if(auth()->user()->unread_notifications_count > 0)
                                <span class="notif-dot"></span>
                            @endif
                        </button>
                        <div id="notif-dropdown" class="hidden absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                <span class="font-display font-bold text-sm">Notifications</span>
                                <span class="badge badge-purple">{{ auth()->user()->unread_notifications_count }}</span>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                @forelse(auth()->user()->notifications_custom()->latest()->take(5)->get() as $notif)
                                    <div class="p-3 hover:bg-gray-50 border-b border-gray-50 {{ !$notif->is_read ? 'bg-purple-50' : '' }}">
                                        <p class="text-xs font-semibold text-gray-800">{{ $notif->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $notif->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-gray-400 text-sm">Aucune notification</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative">
                        <button id="user-menu-btn" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-9 h-9 rounded-xl object-cover">
                            <div class="hidden md:block text-left">
                                <p class="text-xs font-semibold text-gray-800 leading-none">{{ Str::limit(auth()->user()->name, 15) }}</p>
                                <p class="text-xs text-purple-600 font-medium capitalize mt-0.5">{{ auth()->user()->role }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>
                        <div id="user-dropdown" class="hidden absolute right-0 top-12 w-52 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden py-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-primary transition-colors">
                                <i class="fas fa-th-large w-4 text-center"></i> Tableau de bord
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-primary transition-colors">
                                <i class="fas fa-user w-4 text-center"></i> Mon profil
                            </a>
                            @if(auth()->user()->isEtudiant())
                            <a href="{{ route('projects.mine') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-primary transition-colors">
                                <i class="fas fa-folder w-4 text-center"></i> Mes projets
                            </a>
                            @endif
                            @if(auth()->user()->isContributeur())
                            <a href="{{ route('contributions.mine') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-primary transition-colors">
                                <i class="fas fa-heart w-4 text-center"></i> Mes contributions
                            </a>
                            @endif
                            <hr class="my-2 border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 w-full transition-colors">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
<div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="alert-success">
        <i class="fas fa-check-circle text-green-500 text-lg"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

@if($errors->any())
<div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="alert-error">
        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-white mt-24">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg,#6C3BEE,#F59E0B)">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <span class="font-display font-bold text-xl">EduFund</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">La plateforme de financement participatif dédiée aux étudiants innovants.</p>
                <div class="flex gap-3 mt-4">
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-twitter text-sm"></i></a>
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-linkedin-in text-sm"></i></a>
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-instagram text-sm"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-display font-bold mb-4">Plateforme</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('projects.index') }}" class="hover:text-white transition-colors">Explorer les projets</a></li>
                    <li><a href="{{ route('partners.index') }}" class="hover:text-white transition-colors">Nos partenaires</a></li>
                    <li><a href="{{ route('offers.index') }}" class="hover:text-white transition-colors">Opportunités</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold mb-4">Pour les étudiants</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Créer un projet</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Guide du créateur</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Assistant IA</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold mb-4">Contact</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li class="flex items-center gap-2"><i class="fas fa-envelope text-primary text-xs"></i> contact@edufund.tn</li>
                    <li class="flex items-center gap-2"><i class="fas fa-phone text-primary text-xs"></i> +216 71 000 000</li>
                    <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-primary text-xs"></i> Tunis, Tunisie</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-gray-500 text-sm">© {{ date('Y') }} EduFund. Tous droits réservés.</p>
            <p class="text-gray-500 text-sm">Fait avec <i class="fas fa-heart text-red-400 mx-1"></i> en Tunisie</p>
        </div>
    </div>
</footer>

<!-- AI Chatbot Button (only for logged etudiant) -->
@auth
@if(auth()->user()->isEtudiant())
<button id="ai-chat-toggle" class="fixed bottom-6 right-6 w-14 h-14 rounded-2xl flex items-center justify-center text-white z-50 shadow-2xl pulse-glow"
    style="background: linear-gradient(135deg, #6C3BEE, #F59E0B)">
    <i class="fas fa-robot text-xl" id="ai-icon"></i>
</button>

<!-- AI Chat Window -->
<div id="ai-chat-window">
    <!-- Header -->
    <div class="p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #6C3BEE, #8B5CF6)">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fas fa-robot text-white"></i>
            </div>
            <div>
                <p class="font-display font-bold text-white text-sm">Assistant EduFund IA</p>
                <p class="text-white/70 text-xs">Aide à la création de projet</p>
            </div>
        </div>
        <button id="ai-chat-close" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Messages -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3 bg-white" style="min-height: 300px; max-height: 350px;">
        <div class="chat-msg-ai">
            Bonjour ! 👋 Je suis ton assistant IA EduFund.<br><br>
            Je peux t'aider à :<br>
            ✨ Rédiger ton titre et ta description<br>
            💰 Estimer ton budget<br>
            📊 Améliorer ton impact social<br><br>
            Comment puis-je t'aider aujourd'hui ?
        </div>
    </div>

    <!-- Input -->
    <div class="p-3 bg-white border-t border-gray-100 flex gap-2">
        <input id="chat-input" type="text" placeholder="Pose ta question..."
            class="flex-1 px-3 py-2 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary focus:outline-none">
        <button id="chat-send" class="w-9 h-9 rounded-xl flex items-center justify-center text-white flex-shrink-0"
            style="background: linear-gradient(135deg, #6C3BEE, #8B5CF6)">
            <i class="fas fa-paper-plane text-sm"></i>
        </button>
    </div>
</div>
@endif
@endauth

<!-- JS -->
<script>
// Dropdown menus
document.getElementById('user-menu-btn')?.addEventListener('click', (e) => {
    e.stopPropagation();
    document.getElementById('user-dropdown').classList.toggle('hidden');
    document.getElementById('notif-dropdown').classList.add('hidden');
});
document.getElementById('notif-btn')?.addEventListener('click', (e) => {
    e.stopPropagation();
    document.getElementById('notif-dropdown').classList.toggle('hidden');
    document.getElementById('user-dropdown').classList.add('hidden');
});
document.addEventListener('click', () => {
    document.getElementById('user-dropdown')?.classList.add('hidden');
    document.getElementById('notif-dropdown')?.classList.add('hidden');
});

// AI Chatbot
const chatToggle = document.getElementById('ai-chat-toggle');
const chatWindow = document.getElementById('ai-chat-window');
const chatClose = document.getElementById('ai-chat-close');
const chatInput = document.getElementById('chat-input');
const chatSend = document.getElementById('chat-send');
const chatMessages = document.getElementById('chat-messages');

let conversationHistory = [];

chatToggle?.addEventListener('click', () => {
    chatWindow.classList.toggle('open');
    document.getElementById('ai-icon').className = chatWindow.classList.contains('open') ? 'fas fa-times text-xl' : 'fas fa-robot text-xl';
});
chatClose?.addEventListener('click', () => {
    chatWindow.classList.remove('open');
    document.getElementById('ai-icon').className = 'fas fa-robot text-xl';
});

async function sendChatMessage() {
    const msg = chatInput.value.trim();
    if (!msg) return;

    chatInput.value = '';
    addChatMessage(msg, 'user');
    conversationHistory.push({ role: 'user', content: msg });

    // Loading
    const loadingId = 'loading-' + Date.now();
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'chat-msg-ai';
    loadingDiv.id = loadingId;
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En train de réfléchir...';
    chatMessages.appendChild(loadingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        const res = await fetch('/ai/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                message: msg,
                conversation_history: conversationHistory.slice(-6),
            }),
        });
        const data = await res.json();
        document.getElementById(loadingId)?.remove();

        if (data.success) {
            addChatMessage(data.message, 'ai');
            conversationHistory.push({ role: 'assistant', content: data.message });
        } else {
            addChatMessage('Désolé, une erreur s\'est produite. Réessayez.', 'ai');
        }
    } catch (e) {
        document.getElementById(loadingId)?.remove();
        addChatMessage('Erreur de connexion.', 'ai');
    }
}

function addChatMessage(text, role) {
    const div = document.createElement('div');
    div.className = role === 'user' ? 'chat-msg-user' : 'chat-msg-ai';
    div.textContent = text;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

chatSend?.addEventListener('click', sendChatMessage);
chatInput?.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendChatMessage(); });

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `px-4 py-3 rounded-xl text-sm font-medium shadow-lg flex items-center gap-2 animate-fade-up ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>${message}`;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>

@stack('scripts')
</body>
</html>
