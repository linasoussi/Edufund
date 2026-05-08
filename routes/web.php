<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Route;

// ===================== PUBLIC ROUTES =====================
Route::get('/', function () {
    $featuredProjects = \App\Models\Project::with('user')->published()
        ->orderBy('amount_raised', 'desc')->take(6)->get();
    $stats = [
        'projects' => \App\Models\Project::where('status', 'published')->count(),
        'raised' => \App\Models\Contribution::where('status', 'completed')->sum('amount'),
        'contributors' => \App\Models\User::where('role', 'contributeur')->count(),
        'students' => \App\Models\User::where('role', 'etudiant')->count(),
    ];
    return view('home', compact('featuredProjects', 'stats'));
})->name('home');

// Projects public (avec ordre corrigé : routes fixes avant la route dynamique)
Route::get('/projets', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projets/recherche', [ProjectController::class, 'search'])->name('projects.search');

// ⚠️ Route de création : doit être AVANT la route dynamique {project:slug}
Route::get('/projets/creer', [ProjectController::class, 'create'])
    ->name('projects.create')
    ->middleware('auth');  // accessible uniquement aux utilisateurs connectés

// Route dynamique (publique) : à placer APRÈS toutes les routes fixes
Route::get('/projets/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

// Partenaires public
Route::get('/partenaires', [PartnerController::class, 'listPartners'])->name('partners.index');
Route::get('/partenaires/{user}', [PartnerController::class, 'showPartner'])->name('partners.show');

// Offres public
Route::get('/offres', [OfferController::class, 'index'])->name('offers.index');

// ===================== AUTH ROUTES (non connecté) =====================
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===================== AUTH REQUIRED ROUTES (connecté) =====================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil/modifier', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/modifier', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/mot-de-passe', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profil/supprimer', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects (autres actions, sauf create qui est déjà définie plus haut)
    Route::post('/projets', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projets/{project:slug}/modifier', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projets/{project:slug}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projets/{project:slug}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projets/{project}/publier', [ProjectController::class, 'publish'])->name('projects.publish');
    Route::post('/projets/{project}/cloturer', [ProjectController::class, 'close'])->name('projects.close');
    Route::post('/projets/{project}/suivre', [ProjectController::class, 'follow'])->name('projects.follow');
    Route::get('/mes-projets', [ProjectController::class, 'myProjects'])->name('projects.mine');

    // Contributions (Contributeur only)
    Route::get('/projets/{project}/contribuer', [ContributionController::class, 'showForm'])->name('contributions.form');
    Route::post('/projets/{project}/contribuer', [ContributionController::class, 'store'])->name('contributions.store');
    Route::get('/mes-contributions', [ContributionController::class, 'myContributions'])->name('contributions.mine');

    // Comments
    Route::post('/projets/{project}/commentaires', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/commentaires/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/commentaires/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Partnerships (Partenaire)
    Route::post('/partenariats/proposer', [PartnerController::class, 'proposePartnership'])->name('partnerships.propose');
    Route::put('/partenariats/{partnership}/repondre', [PartnerController::class, 'respondPartnership'])->name('partnerships.respond');

    // Offers (Partenaire)
    Route::post('/offres', [OfferController::class, 'store'])->name('offers.store');
    Route::delete('/offres/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');
    Route::patch('/offres/{offer}/toggle', [OfferController::class, 'toggle'])->name('offers.toggle');

    // AI Routes
    Route::post('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');
    Route::post('/ai/predict', [AIController::class, 'predictSuccess'])->name('ai.predict');
    Route::post('/ai/save/{project}', [AIController::class, 'saveAnalysis'])->name('ai.save');
    
});