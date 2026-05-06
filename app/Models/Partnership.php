<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    protected $fillable = [
    'partner_id',
    'partenaire_id',
    'student_id',
    'projet_id',
    'title',
    'description',
    'type',
    'status',
    'message',        // ← ajout
];

    public function partner() { return $this->belongsTo(User::class, 'partner_id'); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function project()
{
    return $this->belongsTo(Project::class, 'projet_id');
}
}
