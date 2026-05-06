<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'short_description', 'description',
        'category', 'cover_image', 'funding_goal', 'amount_raised',
        'deadline', 'status', 'success_score', 'ai_suggestions',
        'views_count', 'video_url', 'tags',
    ];

    protected $casts = [
        'deadline' => 'date',
        'funding_goal' => 'decimal:2',
        'amount_raised' => 'decimal:2',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            $project->slug = Str::slug($project->title) . '-' . uniqid();
        });
    }

    // Relations (inchangées)
    public function user() { return $this->belongsTo(User::class); }
    public function contributions() { return $this->hasMany(Contribution::class); }
    public function comments() { return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies.user')->latest(); }
    public function images() { return $this->hasMany(ProjectImage::class)->orderBy('order'); }
    public function followers() { return $this->belongsToMany(User::class, 'project_follows')->withTimestamps(); }
    public function partnerships() { return $this->hasMany(Partnership::class); }

    // Accesseurs
    public function getProgressPercentageAttribute(): float
    {
        if ($this->funding_goal <= 0) return 0;
        return min(100, round(($this->amount_raised / $this->funding_goal) * 100, 1));
    }

    public function getDaysLeftAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline->isPast();
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/default-project.jpg');
    }

    public function getContributorsCountAttribute(): int
    {
        return $this->contributions()->where('status', 'completed')->distinct('user_id')->count('user_id');
    }

    // Scopes
    public function scopePublished($query) { return $query->where('status', 'published'); }
    public function scopeByCategory($query, $category) { return $category ? $query->where('category', $category) : $query; }
}