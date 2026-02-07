<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use App\Models\Organization;

trait HasActivityLog
{
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function logActivity($action, $description, $oldValues = null, $newValues = null)
    {
        // loggable_type and loggable_id auto-filled because of activityLogs
        return  $this->activityLogs()->create([ 
            'organization_id' => $this->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}