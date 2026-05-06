<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Contribution;
use App\Models\Offer;
use App\Models\Partnership;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isEtudiant()) {
            $stats = [
                'total_projects' => $user->projects()->count(),
                'published_projects' => $user->projects()->where('status', 'published')->count(),
                'total_raised' => $user->projects()->sum('amount_raised'),
                'total_contributors' => Contribution::whereIn('project_id', $user->projects()->pluck('id'))
                    ->where('status', 'completed')
                    ->distinct('user_id')->count(),
            ];
            $projects = $user->projects()->latest()->take(5)->get();
            $notifications = $user->notifications_custom()->latest()->take(5)->get();

            // Récupérer les propositions de partenariat reçues
            $partnershipRequests = Partnership::where('student_id', $user->id)
                ->with(['partner', 'project'])
                ->latest()
                ->get();
            return view('dashboard.etudiant', compact('stats', 'projects', 'notifications', 'partnershipRequests'));
        }

        if ($user->isContributeur()) {
            $stats = [
                'total_contributions' => $user->contributions()->where('status', 'completed')->count(),
                'total_amount' => $user->contributions()->where('status', 'completed')->sum('amount'),
                'followed_projects' => $user->followedProjects()->count(),
                'funded_projects' => $user->contributions()->where('status', 'completed')
                    ->distinct('project_id')->count(),
            ];
            $recentContributions = $user->contributions()->with('project')->latest()->take(5)->get();
            $followedProjects = $user->followedProjects()->latest()->take(6)->get();
            return view('dashboard.contributeur', compact('stats', 'recentContributions', 'followedProjects'));
        }

        if ($user->isPartenaire()) {
            $stats = [
                'total_offers' => $user->offers()->count(),
                'active_offers' => $user->offers()->where('is_active', true)->count(),
                'partnerships' => $user->partnerships()->count(),
                'accepted_partnerships' => $user->partnerships()->where('status', 'accepted')->count(),
            ];
            $offers = $user->offers()->latest()->take(5)->get();
            $partnerships = $user->partnerships()->with('student', 'project')->latest()->take(5)->get();
            return view('dashboard.partenaire', compact('stats', 'offers', 'partnerships'));
        }

        return redirect()->route('home');
    }
}
