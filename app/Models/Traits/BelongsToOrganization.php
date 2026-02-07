<?php

namespace App\Models\Traits;

use App\Models\Organization;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization()
    {
        // Automatically set organization_id when creating
        static::creating(function ($model) {
            if (!$model->organization_id && auth()->check()) {
                $model->organization_id = auth()->user()->current_organization_id;
            }
        });
    }

    public function organization()
    {
        return $this->beloongsTo(Organization::class);
    }
}