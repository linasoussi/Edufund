<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with('user')->where('is_active', true)->latest()->paginate(12);
        return view('offers.index', compact('offers'));
    }

    public function store(Request $request)
{
    if (auth()->user()->role !== 'partenaire') {
        abort(403, 'Seuls les partenaires peuvent créer des offres.');
    }

    $rules = [
        'description' => 'required|string',
        'type' => 'required|in:stage,mentorat,emploi,collaboration,financement',
        'location' => 'nullable|string|max:255',
        'deadline' => 'nullable|date|after:today',
        'contact_email' => 'nullable|email',
    ];

    // Accepter soit 'title', soit 'titre'
    if ($request->has('title')) {
        $rules['title'] = 'required|string|max:255';
    } elseif ($request->has('titre')) {
        $rules['titre'] = 'required|string|max:255';
    } else {
        return back()->withErrors(['title' => 'Le champ titre est obligatoire.'])->withInput();
    }

    $validated = $request->validate($rules);

    $title = $validated['title'] ?? $validated['titre'] ?? null;

    Offer::create([
        'user_id' => Auth::id(),
        'title' => $title,
        'description' => $validated['description'],
        'type' => $validated['type'],
        'location' => $validated['location'] ?? null,
        'deadline' => $validated['deadline'] ?? null,
        'contact_email' => $validated['contact_email'] ?? null,
        'is_active' => true,
    ]);

    return back()->with('success', 'Offre publiée avec succès !');
}

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return back()->with('success', 'Offre supprimée.');
    }

    public function toggle(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        return back()->with('success', $offer->is_active ? 'Offre activée.' : 'Offre désactivée.');
    }
}