<?php

// ============================================
// App\Http\Controllers\PartnerController.php
// ============================================
namespace App\Http\Controllers;

use App\Models\Partnership;
use App\Models\Project;
use App\Models\User;
use App\Models\NotificationCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function proposePartnership(Request $request)
{
    $request->validate([
        'student_id' => ['required', 'exists:users,id'],
        'project_id' => ['nullable', 'exists:projects,id'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'type' => ['required', 'in:mentorat,financement,stage,collaboration,autre'],
    ]);

    $partnership = Partnership::create([
        'partner_id' => Auth::id(),
        'student_id' => $request->student_id,
        'project_id' => $request->project_id,
        'title' => $request->title,
        'description' => $request->description,
        'type' => $request->type,
        'status' => 'pending',
    ]);

    NotificationCustom::create([
        'user_id' => $request->student_id,
        'type' => 'partnership',
        'title' => 'Nouvelle proposition de partenariat !',
        'message' => Auth::user()->name . ' vous propose un partenariat : ' . $request->title,
        'link' => route('dashboard'),
    ]);

    return back()->with('success', 'Proposition de partenariat envoyée !');
}
    public function respondPartnership(Request $request, Partnership $partnership)
    {
        abort_if($partnership->student_id !== Auth::id(), 403);

        $request->validate(['status' => ['required', 'in:accepted,refused']]);

        $partnership->update(['status' => $request->status]);

        NotificationCustom::create([
            'user_id' => $partnership->partner_id,
            'type' => 'partnership_response',
            'title' => $request->status === 'accepted' ? 'Partenariat accepté ! 🎉' : 'Partenariat refusé',
            'message' => Auth::user()->name . ' a ' . ($request->status === 'accepted' ? 'accepté' : 'refusé') . ' votre proposition : ' . $partnership->title,
            'link' => route('dashboard'),
        ]);

        return back()->with('success', 'Réponse envoyée !');
    }

    public function listPartners()
    {
        $partners = User::where('role', 'partenaire')->where('is_active', true)->paginate(12);
        return view('partners.index', compact('partners'));
    }

    public function showPartner(User $user)
    {
        abort_if($user->role !== 'partenaire', 404);
        $user->load('offers');
        return view('partners.show', compact('user'));
    }
}
