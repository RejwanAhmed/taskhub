<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasActivityLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role', 'added_by')
            ->withTimestamps();
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    // Helper Methods

    public function isOrganizationOwner(Organization $organization): bool
    {
        return $this->organizations()
            ->wherePivot('organization_id', $organization->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function isOrganizationManager(Organization $organization): bool
    {
        return $this->organizations()
            ->wherePivot('organization_id', $organization->id)
            ->wherePivot('role', 'manager')
            ->exists();
    }

    public function isProjectManger(Project $project): bool
    {
        return $this->projects()
            ->wherePivot('project_id', $project->id)
            ->wherePivot('role', 'manager')
            ->exist();
    }

    public function isProjectMember(Project $project): bool
    {
        return $this->projects()
            ->wherePivot('project_id', $project->id)
            ->wherePivot('role', 'member')
            ->exist();
    }

}
