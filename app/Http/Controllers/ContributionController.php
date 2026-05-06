<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Project;
use App\Models\NotificationCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContributionController extends Controller
{
    public function showForm(Project $project)
    {
        abort_if(!Auth::check(), 401);
        return view('contributions.form', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:carte_bancaire,paypal,virement,mobile_money'],
            'message' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $contribution = Contribution::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'completed', // Simplified - in production integrate payment gateway
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'message' => $request->message,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        // Update project amount raised
        $project->increment('amount_raised', $request->amount);

        // Check if project is fully funded
        if ($project->amount_raised >= $project->funding_goal) {
            $project->update(['status' => 'funded']);
        }

        // Notify project owner
        NotificationCustom::create([
            'user_id' => $project->user_id,
            'type' => 'contribution',
            'title' => 'Nouvelle contribution reçue !',
            'message' => ($request->boolean('is_anonymous') ? 'Quelqu\'un' : Auth::user()->name)
                . ' a contribué ' . number_format($request->amount, 2) . ' TND à votre projet "' . $project->title . '"',
            'link' => route('projects.show', $project),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', '🎉 Contribution de ' . number_format($request->amount, 2) . ' TND effectuée avec succès !');
    }

    public function myContributions()
    {
        $contributions = Auth::user()->contributions()
            ->with('project')
            ->latest()
            ->paginate(12);

        return view('dashboard.my-contributions', compact('contributions'));
    }
}
