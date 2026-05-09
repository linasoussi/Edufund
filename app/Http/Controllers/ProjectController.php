<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\NotificationCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;   // ← AJOUTÉ

// Gestion des Projets Étudiants
class ProjectController extends Controller
{
     public function index()
    {
        $projects = Project::with('user')
            ->published()
            ->latest()
            ->paginate(12);

        $categories = Project::published()->distinct()->pluck('category');

        return view('projects.index', compact('projects', 'categories'));
    }

    public function show(Project $project)
    {
        // Increment views
        $project->increment('views_count');

        $project->load([
            'user', 'contributions.user', 'comments.user', 'comments.replies.user',
            'images', 'followers',
        ]);

        $isFollowing = auth()->check() && $project->followers->contains(auth()->id());
        $hasContributed = auth()->check() && $project->contributions()
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->exists();

        return view('projects.show', compact('project', 'isFollowing', 'hasContributed'));
    }

    public function create()
    {
        //Gate::authorize('create', Project::class);   // ← MODIFIÉ
        return view('projects.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);   // ← MODIFIÉ

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string'],
            'funding_goal' => ['required', 'numeric', 'min:100'],
            'deadline' => ['required', 'date', 'after:today'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
            'tags' => ['nullable', 'string'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $project = Project::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'funding_goal' => $validated['funding_goal'],
            'deadline' => $validated['deadline'],
            'cover_image' => $coverPath,
            'video_url' => $validated['video_url'] ?? null,
            'tags' => $tags,
            'status' => 'draft',
        ]);

        // Upload gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $path = $image->store('projects/gallery', 'public');
                $project->images()->create(['image_path' => $path, 'order' => $index]);
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Projet créé avec succès !');
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);   // ← MODIFIÉ
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);   // ← MODIFIÉ

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string'],
            'funding_goal' => ['required', 'numeric', 'min:100'],
            'deadline' => ['required', 'date'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
            'tags' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) Storage::disk('public')->delete($project->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];
        $validated['tags'] = $tags;

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Projet mis à jour !');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);   // ← MODIFIÉ

        if ($project->cover_image) Storage::disk('public')->delete($project->cover_image);
        $project->delete();

        return redirect()->route('dashboard')->with('success', 'Projet supprimé.');
    }

    public function publish(Project $project)
    {
        Gate::authorize('update', $project);   // ← MODIFIÉ

        $project->update(['status' => 'published']);

        return back()->with('success', 'Projet publié avec succès ! 🚀');
    }

    public function close(Project $project)
    {
        Gate::authorize('update', $project);   // ← MODIFIÉ

        $project->update(['status' => 'closed']);

        return back()->with('success', 'Campagne clôturée.');
    }

    public function follow(Project $project)
    {
        $user = Auth::user();
        if ($project->followers()->where('user_id', $user->id)->exists()) {
            $project->followers()->detach($user->id);
            return response()->json(['following' => false]);
        } else {
            $project->followers()->attach($user->id);
            return response()->json(['following' => true]);
        }
    }

    // Dashboard projects for student
    public function myProjects()
    {
        $projects = Auth::user()->projects()->latest()->paginate(10);
        return view('dashboard.my-projects', compact('projects'));
    }

    // Search/filter
    public function search(Request $request)
    {
        $query = Project::with('user')->published();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%$q%")
                   ->orWhere('short_description', 'like', "%$q%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'popular' => $query->orderBy('views_count', 'desc'),
                'funded'  => $query->orderBy('amount_raised', 'desc'),
                'newest'  => $query->latest(),
                'ending'  => $query->orderBy('deadline', 'asc'),
                default   => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $projects = $query->paginate(12)->withQueryString();
        $categories = Project::published()->distinct()->pluck('category');

        return view('projects.index', compact('projects', 'categories'));
    }
}