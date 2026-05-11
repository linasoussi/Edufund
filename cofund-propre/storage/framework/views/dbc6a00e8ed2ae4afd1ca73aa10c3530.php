<?php $__env->startSection('title', $project->title . ' — CoFund'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero -->
<div class="project-hero">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-400">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-white transition-colors">Accueil</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="<?php echo e(route('projects.index')); ?>" class="hover:text-white transition-colors">Projets</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-white"><?php echo e(Str::limit($project->title, 40)); ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 pb-0">
            <!-- Left: Info -->
            <div class="lg:col-span-3 pb-8">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="badge badge-purple"><?php echo e($project->category); ?></span>
                    <?php if($project->success_score): ?>
                    <span class="badge" style="background: linear-gradient(135deg,#6C3BEE22,#F59E0B22); color: #F59E0B; border: 1px solid #F59E0B44">
                        <i class="fas fa-robot text-xs"></i> Score IA: <?php echo e($project->success_score); ?>%
                    </span>
                    <?php endif; ?>
                    <span class="badge <?php echo e($project->status === 'published' ? 'badge-green' : ($project->status === 'funded' ? 'badge-purple' : 'badge-amber')); ?>">
                        <?php echo e(['published' => '🟢 Actif', 'funded' => '🏆 Financé', 'draft' => '📝 Brouillon', 'closed' => '🔒 Clôturé'][$project->status] ?? $project->status); ?>

                    </span>
                </div>

                <h1 class="font-display text-3xl md:text-4xl font-bold text-white mb-4 leading-tight">
                    <?php echo e($project->title); ?>

                </h1>
                <p class="text-gray-300 text-lg leading-relaxed mb-6"><?php echo e($project->short_description); ?></p>

                <!-- Creator -->
                <div class="flex items-center gap-3">
                    <img src="<?php echo e($project->user->avatar_url); ?>" class="w-11 h-11 rounded-xl object-cover border-2 border-purple-500/40">
                    <div>
                        <p class="font-semibold text-white"><?php echo e($project->user->name); ?></p>
                        <p class="text-gray-400 text-sm">
                            <?php echo e($project->user->university ?? 'Étudiant'); ?>

                            <?php if($project->user->field_of_study): ?> · <?php echo e($project->user->field_of_study); ?> <?php endif; ?>
                        </p>
                    </div>
                    <div class="ml-auto flex items-center gap-3 text-gray-400 text-sm">
                        <span><i class="fas fa-eye mr-1"></i><?php echo e(number_format($project->views_count)); ?></span>
                        <span><i class="fas fa-users mr-1"></i><?php echo e($project->contributors_count); ?></span>
                        <?php if(auth()->guard()->check()): ?>
                        <button id="follow-btn" onclick="toggleFollow(<?php echo e($project->id); ?>)"
                            class="btn-outline text-xs py-1.5 px-3 <?php echo e($isFollowing ? 'bg-purple-100' : ''); ?>" style="color: white; border-color: rgba(255,255,255,0.3)">
                            <i class="fas fa-<?php echo e($isFollowing ? 'bookmark' : 'bookmark'); ?> text-xs"></i>
                            <span id="follow-text"><?php echo e($isFollowing ? 'Suivi' : 'Suivre'); ?></span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Cover -->
            <div class="lg:col-span-2">
                <img src="<?php echo e($project->cover_image_url); ?>" alt="<?php echo e($project->title); ?>"
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
                <button class="tab-btn" onclick="switchTab('comments')">💬 Commentaires <span class="badge badge-purple ml-1 text-xs"><?php echo e($project->comments->count()); ?></span></button>
            </div>

            <!-- Description Tab -->
            <div id="tab-description" class="tab-content active">
                <div class="prose max-w-none text-gray-700 leading-relaxed mb-6" style="white-space: pre-wrap"><?php echo e($project->description); ?></div>

                <?php if($project->tags && count($project->tags) > 0): ?>
                <div class="flex flex-wrap gap-2 mt-6">
                    <?php $__currentLoopData = $project->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge badge-purple">#<?php echo e($tag); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if($project->video_url): ?>
                <div class="mt-6">
                    <h3 class="font-display font-bold text-gray-900 mb-3">🎥 Vidéo de présentation</h3>
                    <div class="rounded-2xl overflow-hidden bg-black aspect-video">
                        <?php
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $project->video_url, $matches);
                            $videoId = $matches[1] ?? null;
                        ?>
                        <?php if($videoId): ?>
                        <iframe src="https://www.youtube.com/embed/<?php echo e($videoId); ?>" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        <?php else: ?>
                        <a href="<?php echo e($project->video_url); ?>" target="_blank" class="flex items-center justify-center h-40 text-white">
                            <i class="fas fa-play-circle text-4xl"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($project->images->count() > 0): ?>
                <div class="mt-6">
                    <h3 class="font-display font-bold text-gray-900 mb-3">📸 Galerie</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <?php $__currentLoopData = $project->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <img src="<?php echo e($img->image_url); ?>" class="rounded-xl object-cover w-full h-32 cursor-pointer hover:opacity-90 transition-opacity">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Updates Tab -->
            <div id="tab-updates" class="tab-content">
                <div class="card p-6 text-center text-gray-400">
                    <i class="fas fa-chart-line text-4xl mb-3 block text-purple-300"></i>
                    <p class="font-semibold text-gray-600">Suivi de la campagne</p>
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="bg-purple-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-primary"><?php echo e(number_format($project->amount_raised, 0)); ?></p>
                            <p class="text-xs text-gray-500 mt-1">TND collectés</p>
                        </div>
                        <div class="bg-amber-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-amber-600"><?php echo e($project->progress_percentage); ?>%</p>
                            <p class="text-xs text-gray-500 mt-1">de l'objectif</p>
                        </div>
                        <div class="bg-green-50 rounded-2xl p-4">
                            <p class="font-display text-2xl font-bold text-green-600"><?php echo e($project->days_left); ?></p>
                            <p class="text-xs text-gray-500 mt-1">jours restants</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributors Tab -->
            <div id="tab-contributors" class="tab-content">
                <?php $__empty_1 = true; $__currentLoopData = $project->contributions->where('status', 'completed'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrib): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3 p-4 bg-white rounded-2xl mb-3 shadow-sm border border-gray-100">
                    <?php if($contrib->is_anonymous): ?>
                    <div class="w-11 h-11 rounded-xl bg-gray-200 flex items-center justify-center text-gray-500">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Contributeur anonyme</p>
                        <p class="text-xs text-gray-400"><?php echo e($contrib->created_at->diffForHumans()); ?></p>
                    </div>
                    <?php else: ?>
                    <img src="<?php echo e($contrib->user->avatar_url); ?>" class="contributor-avatar">
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo e($contrib->user->name); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($contrib->created_at->diffForHumans()); ?></p>
                        <?php if($contrib->message): ?>
                        <p class="text-xs text-gray-600 mt-0.5 italic">"<?php echo e($contrib->message); ?>"</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="ml-auto text-right">
                        <p class="font-display font-bold text-primary"><?php echo e(number_format($contrib->amount, 0)); ?> TND</p>
                        <p class="text-xs text-gray-400 capitalize"><?php echo e(str_replace('_', ' ', $contrib->payment_method)); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-hand-holding-heart text-4xl mb-3 block text-gray-200"></i>
                    <p>Soyez le premier à contribuer !</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Comments Tab -->
            <div id="tab-comments" class="tab-content">
                <?php if(auth()->guard()->check()): ?>
                <form action="<?php echo e(route('comments.store', $project)); ?>" method="POST" class="mb-6">
                    <?php echo csrf_field(); ?>
                    <div class="flex gap-3">
                        <img src="<?php echo e(auth()->user()->avatar_url); ?>" class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
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
                <?php endif; ?>

                <?php $__empty_1 = true; $__currentLoopData = $project->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="comment-card">
                    <div class="flex items-start gap-3">
                        <img src="<?php echo e($comment->user->avatar_url); ?>" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-gray-800 text-sm"><?php echo e($comment->user->name); ?></span>
                                    <span class="text-xs text-gray-400 ml-2"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                                </div>
                                <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->id() === $comment->user_id): ?>
                                <div class="flex gap-2">
                                    <form action="<?php echo e(route('comments.destroy', $comment)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="text-xs text-red-400 hover:text-red-600">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-700 text-sm mt-1"><?php echo e($comment->content); ?></p>

                            <?php if(auth()->guard()->check()): ?>
                            <button onclick="toggleReply(<?php echo e($comment->id); ?>)" class="text-xs text-primary font-semibold mt-2 hover:underline">
                                <i class="fas fa-reply text-xs"></i> Répondre
                            </button>
                            <div id="reply-<?php echo e($comment->id); ?>" class="hidden mt-3">
                                <form action="<?php echo e(route('comments.store', $project)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="parent_id" value="<?php echo e($comment->id); ?>">
                                    <div class="flex gap-2">
                                        <textarea name="content" rows="2" placeholder="Votre réponse..."
                                            class="form-input resize-none text-xs flex-1"></textarea>
                                        <button type="submit" class="btn-primary text-xs py-2 px-3 self-end">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Replies -->
                    <?php $__currentLoopData = $comment->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="reply-card flex items-start gap-2 mt-2 ml-12">
                        <img src="<?php echo e($reply->user->avatar_url); ?>" class="w-7 h-7 rounded-lg object-cover flex-shrink-0">
                        <div>
                            <span class="font-semibold text-gray-800 text-xs"><?php echo e($reply->user->name); ?></span>
                            <span class="text-xs text-gray-400 ml-1"><?php echo e($reply->created_at->diffForHumans()); ?></span>
                            <p class="text-gray-700 text-xs mt-0.5"><?php echo e($reply->content); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-comments text-3xl mb-2 block text-gray-200"></i>
                    <p class="text-sm">Aucun commentaire. Soyez le premier !</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Sticky Sidebar -->
        <div class="sticky-sidebar">

            <!-- Funding Card -->
            <div class="card p-6 mb-4">
                <div class="mb-4">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="font-display text-2xl font-bold text-gray-900"><?php echo e(number_format($project->amount_raised, 0)); ?> <span class="text-sm font-normal text-gray-400">TND</span></p>
                            <p class="text-xs text-gray-400">collectés sur <?php echo e(number_format($project->funding_goal, 0)); ?> TND</p>
                        </div>
                        <span class="font-display text-xl font-bold text-primary"><?php echo e($project->progress_percentage); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo e($project->progress_percentage); ?>%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5 text-center">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="font-display font-bold text-gray-900 text-lg"><?php echo e($project->contributors_count); ?></p>
                        <p class="text-xs text-gray-400">contributeurs</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="font-display font-bold text-gray-900 text-lg <?php echo e($project->days_left <= 7 ? 'text-red-500' : ''); ?>"><?php echo e($project->days_left); ?></p>
                        <p class="text-xs text-gray-400">jours restants</p>
                    </div>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isContributeur() && $project->status === 'published'): ?>
                    <a href="<?php echo e(route('contributions.form', $project)); ?>" class="btn-primary w-full justify-center text-base py-3.5 mb-3">
                        <i class="fas fa-heart"></i> Contribuer maintenant
                    </a>
                    <?php elseif(auth()->user()->isEtudiant() && auth()->id() === $project->user_id): ?>
                    <div class="space-y-2">
                        <?php if($project->status === 'draft'): ?>
                        <form action="<?php echo e(route('projects.publish', $project)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="btn-primary w-full justify-center py-3">
                                <i class="fas fa-rocket"></i> Publier le projet
                            </button>
                        </form>
                        <?php elseif($project->status === 'published'): ?>
                        <form action="<?php echo e(route('projects.close', $project)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="btn-outline w-full justify-center py-2.5 text-sm">
                                <i class="fas fa-lock"></i> Clôturer la campagne
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="<?php echo e(route('projects.edit', $project)); ?>" class="btn-outline w-full justify-center py-2.5 text-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                    <?php elseif(auth()->user()->isPartenaire()): ?>
                    <button onclick="document.getElementById('partnership-modal').classList.remove('hidden')"
                        class="btn-primary w-full justify-center py-3 mb-2">
                        <i class="fas fa-handshake"></i> Proposer un partenariat
                    </button>
                    <?php endif; ?>
                <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-primary w-full justify-center text-base py-3.5">
                    <i class="fas fa-sign-in-alt"></i> Se connecter pour contribuer
                </a>
                <?php endif; ?>
            </div>

            <!-- Creator Card -->
            <div class="card p-5 mb-4">
                <p class="font-display font-bold text-gray-800 text-sm mb-3">👤 Le porteur du projet</p>
                <div class="flex items-center gap-3">
                    <img src="<?php echo e($project->user->avatar_url); ?>" class="w-12 h-12 rounded-xl object-cover">
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo e($project->user->name); ?></p>
                        <?php if($project->user->university): ?>
                        <p class="text-xs text-gray-500"><?php echo e($project->user->university); ?></p>
                        <?php endif; ?>
                        <?php if($project->user->bio): ?>
                        <p class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($project->user->bio, 60)); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-3 text-xs text-gray-500">
                    <span><i class="fas fa-folder mr-1 text-purple-400"></i><?php echo e($project->user->projects()->where('status', 'published')->count()); ?> projets</span>
                    <span><i class="fas fa-star mr-1 text-amber-400"></i><?php echo e($project->user->projects()->where('status', 'funded')->count()); ?> financés</span>
                </div>
            </div>

            <!-- Share -->
            <div class="card p-5">
                <p class="font-display font-bold text-gray-800 text-sm mb-3">🔗 Partager ce projet</p>
                <div class="flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(request()->url())); ?>" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo e(urlencode(request()->url())); ?>&text=<?php echo e(urlencode($project->title)); ?>" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-gray-900 hover:bg-black transition-colors">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e(urlencode(request()->url())); ?>" target="_blank"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold text-center text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Partnership Modal -->
<?php if(auth()->guard()->check()): ?>
<?php if(auth()->user()->isPartenaire()): ?>
<div id="partnership-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6)">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display text-xl font-bold">Proposer un partenariat</h3>
            <button onclick="document.getElementById('partnership-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="<?php echo e(route('partnerships.propose')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="student_id" value="<?php echo e($project->user_id); ?>">
            <input type="hidden" name="project_id" value="<?php echo e($project->id); ?>">
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
<?php endif; ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cofund-propre\resources\views/projects/show.blade.php ENDPATH**/ ?>