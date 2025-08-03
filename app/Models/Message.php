<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message', 'sender_id', 'seen_at'];
    protected $casts = [
        'seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeLatestMessages($query, $limit = 50)
    {
        return $query->with('user')->latest()->take($limit)->get();
    }
}
