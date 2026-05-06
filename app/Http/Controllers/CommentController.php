<?php

// ============================================
// App\Http\Controllers\CommentController.php
// ============================================
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate(['content' => ['required', 'string', 'max:1000'], 'parent_id' => ['nullable', 'exists:comments,id']]);

        Comment::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Commentaire ajouté !');
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $request->validate(['content' => ['required', 'string', 'max:1000']]);
        $comment->update(['content' => $request->content]);
        return back()->with('success', 'Commentaire modifié !');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success', 'Commentaire supprimé.');
    }
}
