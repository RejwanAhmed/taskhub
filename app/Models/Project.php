<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganization;

class Project extends Model
{
    use HasFactory, BelongsToOrganization, HasActivityLog;
    protected $fillable = ['organization_id', 'name', 'slug', 'description', 'status', 'color', 'start_date', 'end_date', 'created_by'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // protected $appends = ['status_label'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role', 'added_by')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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


    // Helper Methods
    public function managers()
    {
        return $this->members()->wherePivot('role', 'manager');
    }

    public function regularMembers()
    {
        return $this->members()->wherePivot('role', 'member');
    }

    // Query scopes
    public function scropActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePlanning($query)
    {
        return $query->where('status', 'planning');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
