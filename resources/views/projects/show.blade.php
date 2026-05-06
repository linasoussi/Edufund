@extends('layouts.app')
@section('title', $project->title . ' — CoFund')

@push('styles')
<style>
.project-hero {
    background: linear-gradient(135deg, #0A0A0F, #1C1028);
    padding: 3rem 0 0;
}
.sticky-sidebar { position: sticky; top: 80px; }
.tab-btn {
    padding: 0.6rem 1.25rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    background: transparent;
}
.tab-btn.active { background: white; color: #6C3BEE; box-shadow: 0 2px 8px rgba(108,59,238,0.15); }
.tab-content { display: none; }
.tab-content.active { display: block; }
.comment-card { border-radius: 14px; padding: 1rem; background: #F9FAFB; margin-bottom: 0.75rem; }
.reply-card { margin-left: 2.5rem; margin-top: 0.5rem; border-radius: 12px; padding: 0.75rem; background: #F3F4F6; }
.contributor-avatar {
    width: 40px; height: 40px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="project-hero">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('projects.index') }}" class="hover:text-white transition-colors">Projets</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-white">{{ Str::limit($project->title, 40) }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 pb-0">
            <!-- Left: Info -->
            <div class="lg:col-span-3 pb-8">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="badge badge-purple">{{ $project->category }}</span>
                    @if($project->success_score)
                    <span class="badge" style="background: linear-gradient(135deg,#6C3BEE22,#F59E0B22); color: #F59E0B; border: 1px solid #F59E0B44">
                        <i class="fas fa-robot text-xs"></i> Score IA: {{ $project->success_score }}%
                    </span>
                    @endif
                    <span class="badge {{ $project->status === 'published' ? 'badge-green' : ($project->status === 'funded' ? 'badge-purple' : 'badge-amber') }}">
                        {{ ['published' => '🟢 Actif', 'funded' => '🏆 Financé', 'draft' => '📝 Brouillon', 'closed' => '🔒 Clôturé'][$project->status] ?? $project->status }}
                    </span>
                </div>

                <h1 class="font-display text-3xl md:text-4xl font-bold text-white mb-4 leading-tight">
                    {{ $project->title }}
                </h1>
                <p class="text-gray-300 text-lg leading-relaxed mb-6">{{ $project->short_description }}</p>

                <!-- Creator -->
                <div class="flex items-center gap-3">
                    <img src="{{ $project->user->avatar_url }}" class="w-11 h-11 rounded-xl object-cover border-2 border-purple-500/40">
                    <div>
                        <p class="font-semibold text-white">{{ $project->user->name }}</p>
                        <p class="text-gray-400 text-sm">
                            {{ $project->user->university ?? 'Étudiant' }}
                            @if($project->user->field_of_study) · {{ $project->user->field_of_study }} @endif
                        </p>
                    </div>
                    <div class="ml-auto flex items-center gap-3 text-gray-400 text-sm">
                        <span><i class="fas fa-eye mr-1"></i>{{ number_format($project->views_count) }}</span>
                        <span><i class="fas fa-users mr-1"></i>{{ $project->contributors_count }}</span>
                        @auth
                        <button id="follow-btn" onclick="toggleFollow({{ $project->id }})"
                            class="btn-outline text-xs py-1.5 px-3 {{ $isFollowing ? 'bg-purple-100' : '' }}" style="color: white; border-color: rgba(255,255,255,0.3)">
                            <i class="fas fa-{{ $isFollowing ? 'bookmark' : 'bookmark' }} text-xs"></i>
                            <span id="follow-text">{{ $isFollowing ? 'Suivi' : 'Suivre' }}</span>
                        </button>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Right: Cover -->
            <div class="lg:col-span-2">
                <img src="{{ $project->cover_image_url }}" alt="{{ $project->title }}"
                    class="w-full rounded-t-2xl object-cover" style="height: 280px">
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT: Tabs -->
        <div class="lg:col-span-2">
            <!-- Tab Nav -->
            <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl mb-6 w-fit">
                <button class="tab-btn active" onclick="switchTab('description')">📋 Description</button>
                <button class="tab-btn" onclick="switchTab('updates')">📊 Suivi</button>
                <button class="tab-btn" onclick="switchTab('contributors')">💰 Contributeurs</button>
                <button class="tab-btn" onclick="switchTab('comments')">💬 Commentaires <span class="badge badge-purple ml-1 text-xs">{{ $project->comments->count() }}</span></button>
            </div>

            <!-- Description Tab -->
            <div id="tab-description" class="tab-content active">
                <div class="prose max-w-none text-gray-700 leading-relaxed mb-6" style="white-space: pre-wrap">{{ $project->description }}</div>

                @if($project->tags && count($project->tags) > 0)
                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach($project->tags as $tag)
                    <span class="badge badge-purple">#{{ $tag }}</span>
                    @endforeach
                </div>
                @endif

                @if($project->video_url)
                <div class="mt-6">
                    <h3 class="font-display font-bold text-gray-900 mb-3">🎥 Vidéo de présentation</h3>
                    <div class="rounded-2xl overflow-hidden bg-black aspect-video">
                        @php
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $project->video_url, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        @else
                        <a href="{{ $project->video_url }}" target="_blank" class="flex items-center justify-center h-40 text-white">
                            <i class="fas fa-play-circle text-4xl"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if($project->images->count() > 0)
                <div class="mt-6">
                    <h3 class="font-display font-bold text-gray-900 mb-3">📸 Galerie</h3>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($project->images as $img)
                        <img src="{{ $img->image_url }}" class="rounded-xl object-cover w-full h-32 cursor-pointer hover:opacity-90 transition-opacity">
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Updates Tab -->
            <div id="tab-updates" class="tab-content">
                <div class="card p-6 text-center text-gray-400">
                    <i class="fas fa-chart-line text-4xl mb-3 block text-purple-300"></i>
                    <p class="font-semibold text-gray-600">Suivi de la campagne</p>
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="bg-purple-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-primary">{{ number_format($project->amount_raised, 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">TND collectés</p>
                        </div>
                        <div class="bg-amber-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-amber-600">{{ $project->progress_percentage }}%</p>
                            <p class="text-xs text-gray-500 mt-1">de l'objectif</p>
                        </div>
                        <div class="bg-green-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-green-600">{{ $project->days_left }}</p>
                            <p class="text-xs text-gray-500 mt-1">jours restants</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributors Tab -->
            <div id="tab-contributors" class="tab-content">
                @forelse($project->contributions->where('status', 'completed') as $contrib)
                <div class="flex items-center gap-3 p-4 bg-white rounded-2xl mb-3 shadow-sm border border-gray-100">
                    @if($contrib->is_anonymous)
                    <div class="w-11 h-11 rounded-xl bg-gray-200 flex items-center justify-center text-gray-500">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Contributeur anonyme</p>
                        <p class="text-xs text-gray-400">{{ $contrib->created_at->diffForHumans() }}</p>
                    </div>
                    @else
                    <img src="{{ $contrib->user->avatar_url }}" class="contributor-avatar">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $contrib->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $contrib->created_at->diffForHumans() }}</p>
                        @if($contrib->message)
                        <p class="text-xs text-gray-600 mt-0.5 italic">"{{ $contrib->message }}"</p>
                        @endif
                    </div>
                    @endif
                    <div class="ml-auto text-right">
                        <p class="font-display font-bold text-primary">{{ number_format($contrib->amount, 0) }} TND</p>
                        <p class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $contrib->payment_method) }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-hand-holding-heart text-4xl mb-3 block text-gray-200"></i>
                    <p>Soyez le premier à contribuer !</p>
                </div>
                @endforelse
            </div>

            <!-- Comments Tab -->
            <div id="tab-comments" class="tab-content">
                @auth
                <form action="{{ route('comments.store', $project) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex gap-3">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1">
                            <textarea name="content" rows="3" placeholder="Partagez votre avis ou posez une question..."
                                class="form-input resize-none text-sm"></textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="btn-primary text-sm py-2 px-4">
                                    <i class="fas fa-paper-plane text-xs"></i> Commenter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @endauth

                @forelse($project->comments as $comment)
                <div class="comment-card">
                    <div class="flex items-start gap-3">
                        <img src="{{ $comment->user->avatar_url }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                @auth
                                @if(auth()->id() === $comment->user_id)
                                <div class="flex gap-2">
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-400 hover:text-red-600">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                                @endauth
                            </div>
                            <p class="text-gray-700 text-sm mt-1">{{ $comment->content }}</p>

                            @auth
                            <button onclick="toggleReply({{ $comment->id }})" class="text-xs text-primary font-semibold mt-2 hover:underline">
                                <i class="fas fa-reply text-xs"></i> Répondre
                            </button>
                            <div id="reply-{{ $comment->id }}" class="hidden mt-3">
                                <form action="{{ route('comments.store', $project) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="flex gap-2">
                                        <textarea name="content" rows="2" placeholder="Votre réponse..."
                                            class="form-input resize-none text-xs flex-1"></textarea>
                                        <button type="submit" class="btn-primary text-xs py-2 px-3 self-end">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Replies -->
                    @foreach($comment->replies as $reply)
                    <div class="reply-card flex items-start gap-2 mt-2 ml-12">
                        <img src="{{ $reply->user->avatar_url }}" class="w-7 h-7 rounded-lg object-cover flex-shrink-0">
                        <div>
                            <span class="font-semibold text-gray-800 text-xs">{{ $reply->user->name }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $reply->created_at->diffForHumans() }}</span>
                            <p class="text-gray-700 text-xs mt-0.5">{{ $reply->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-comments text-3xl mb-2 block text-gray-200"></i>
                    <p class="text-sm">Aucun commentaire. Soyez le premier !</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Sticky Sidebar -->
        <div class="sticky-sidebar">

            <!-- Funding Card -->
            <div class="card p-6 mb-4">
                <div class="mb-4">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="font-display text-2xl font-bold text-gray-900">{{ number_format($project->amount_raised, 0) }} <span class="text-sm font-normal text-gray-400">TND</span></p>
                            <p class="text-xs text-gray-400">collectés sur {{ number_format($project->funding_goal, 0) }} TND</p>
                        </div>
                        <span class="font-display text-xl font-bold text-primary">{{ $project->progress_percentage }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $project->progress_percentage }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5 text-center">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="font-display font-bold text-gray-900 text-lg">{{ $project->contributors_count }}</p>
                        <p class="text-xs text-gray-400">contributeurs</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="font-display font-bold text-gray-900 text-lg {{ $project->days_left <= 7 ? 'text-red-500' : '' }}">{{ $project->days_left }}</p>
                        <p class="text-xs text-gray-400">jours restants</p>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->isContributeur() && $project->status === 'published')
                    <a href="{{ route('contributions.form', $project) }}" class="btn-primary w-full justify-center text-base py-3.5 mb-3">
                        <i class="fas fa-heart"></i> Contribuer maintenant
                    </a>
                    @elseif(auth()->user()->isEtudiant() && auth()->id() === $project->user_id)
                    <div class="space-y-2">
                        @if($project->status === 'draft')
                        <form action="{{ route('projects.publish', $project) }}" method="POST">
                            @csrf
                            <button class="btn-primary w-full justify-center py-3">
                                <i class="fas fa-rocket"></i> Publier le projet
                            </button>
                        </form>
                        @elseif($project->status === 'published')
                        <form action="{{ route('projects.close', $project) }}" method="POST">
                            @csrf
                            <button class="btn-outline w-full justify-center py-2.5 text-sm">
                                <i class="fas fa-lock"></i> Clôturer la campagne
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('projects.edit', $project) }}" class="btn-outline w-full justify-center py-2.5 text-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                    @elseif(auth()->user()->isPartenaire())
                    <button onclick="document.getElementById('partnership-modal').classList.remove('hidden')"
                        class="btn-primary w-full justify-center py-3 mb-2">
                        <i class="fas fa-handshake"></i> Proposer un partenariat
                    </button>
                    @endif
                @else
                <a href="{{ route('login') }}" class="btn-primary w-full justify-center text-base py-3.5">
                    <i class="fas fa-sign-in-alt"></i> Se connecter pour contribuer
                </a>
                @endauth
            </div>

            <!-- Creator Card -->
            <div class="card p-5 mb-4">
                <p class="font-display font-bold text-gray-800 text-sm mb-3">👤 Le porteur du projet</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $project->user->avatar_url }}" class="w-12 h-12 rounded-xl object-cover">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $project->user->name }}</p>
                        @if($project->user->university)
                        <p class="text-xs text-gray-500">{{ $project->user->university }}</p>
                        @endif
                        @if($project->user->bio)
                        <p class="text-xs text-gray-400 mt-1">{{ Str::limit($project->user->bio, 60) }}</p>
                        @endif
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-3 text-xs text-gray-500">
                    <span><i class="fas fa-folder mr-1 text-purple-400"></i>{{ $project->user->projects()->where('status', 'published')->count() }} projets</span>
                    <span><i class="fas fa-star mr-1 text-amber-400"></i>{{ $project->user->projects()->where('status', 'funded')->count() }} financés</span>
                </div>
            </div>

            <!-- Share -->
            <div class="card p-5">
                <p class="font-display font-bold text-gray-800 text-sm mb-3">🔗 Partager ce projet</p>
                <div class="flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($project->title) }}" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-gray-900 hover:bg-black transition-colors">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Partnership Modal -->
@auth
@if(auth()->user()->isPartenaire())
<div id="partnership-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6)">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display text-xl font-bold">Proposer un partenariat</h3>
            <button onclick="document.getElementById('partnership-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('partnerships.propose') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $project->user_id }}">
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div>
                <label class="form-label">Type de partenariat</label>
                <select name="type" class="form-input">
                    <option value="mentorat">🧑‍🏫 Mentorat</option>
                    <option value="financement">💰 Co-financement</option>
                    <option value="stage">💼 Offre de stage</option>
                    <option value="collaboration">🤝 Collaboration technique</option>
                    <option value="autre">📋 Autre</option>
                </select>
            </div>
            <div>
                <label class="form-label">Titre de la proposition</label>
                <input type="text" name="title" placeholder="ex: Mentorat en développement mobile" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" placeholder="Décrivez votre proposition..." class="form-input resize-none" required></textarea>
            </div>
            <button type="submit" class="btn-primary w-full justify-center py-3">
                <i class="fas fa-paper-plane"></i> Envoyer la proposition
            </button>
        </form>
    </div>
</div>
@endif
@endauth

@endsection

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}

function toggleReply(id) {
    document.getElementById('reply-' + id).classList.toggle('hidden');
}

async function toggleFollow(projectId) {
    const btn = document.getElementById('follow-btn');
    const text = document.getElementById('follow-text');

    try {
        const res = await fetch(`/projets/${projectId}/suivre`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();
        text.textContent = data.following ? 'Suivi ✓' : 'Suivre';
        btn.style.background = data.following ? 'rgba(108,59,238,0.15)' : 'transparent';
    } catch (e) {
        console.error(e);
    }
}
</script>
@endpush
