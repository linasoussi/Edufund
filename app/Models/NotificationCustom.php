<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationCustom extends Model
{
    protected $table = 'notifications_custom';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'link', 'is_read',
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
}
