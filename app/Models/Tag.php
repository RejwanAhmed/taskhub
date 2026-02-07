<?php

namespace App\Models;

use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory, BelongsToOrganization, HasActivityLog;

    protected $fillable = ['organization_id', 'name', 'slug', 'color', 'description'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag')
            ->withTimestamps();
    }
}
