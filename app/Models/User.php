<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'phone', 'bio',
        'university', 'field_of_study', 'company', 'website', 'linkedin', 'is_active',
    ];
//les champs qu’on autorise à remplir automatiquement.
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function partnerships()
    {
        return $this->hasMany(Partnership::class, 'partner_id');
    }

    public function receivedPartnerships()
    {
        return $this->hasMany(Partnership::class, 'student_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function followedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_follows')->withTimestamps();
    }

    public function notifications_custom()
    {
        return $this->hasMany(NotificationCustom::class);
    }

    // Helpers
    public function isEtudiant(): bool
    {
        return $this->role === 'etudiant';
    }

    public function isContributeur(): bool
    {
        return $this->role === 'contributeur';
    }

    public function isPartenaire(): bool
    {
        return $this->role === 'partenaire';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff';
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications_custom()->where('is_read', false)->count();
    }
}
