<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['attachable_type', 'attachable_id', 'user_id', 'file_size', 'file_type', 'file_name', 'mime_type'];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    // protected $appends = ['file_url', 'is_image', 'human_file_size'];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
