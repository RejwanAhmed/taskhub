<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['commentable_type', 'commentable_id', 'user_id', 'content'];

    protected $casts = [
        'created_at' => 'datetime',
        'update_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
