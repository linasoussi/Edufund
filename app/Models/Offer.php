<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
    'user_id',
    'partenaire_id',
    'titre',
    'description',
    'type',
    'location',
    'deadline',
    'is_active',
    'contact_email',
    'contact',          // ← colonne manquante
];

    protected $casts = [
        'deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}