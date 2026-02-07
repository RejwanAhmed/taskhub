<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo', 'description', 'settings', 'status', 'approved_at', 'approved_by'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role', 'joined_at');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    // Helper Methods
    public function owners()
    {
        return $this->members()->wherePivot('role', 'owner');
    }

    public function managers()
    {
        return $this->members()->wherePivot('role', 'manager');
    }

    public function regularMembers()
    {
        return $this->members()->wherePivot('role', 'member');
    }

}
