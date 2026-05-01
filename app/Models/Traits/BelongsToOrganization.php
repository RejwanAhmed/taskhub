<?php

namespace App\Models\Traits;

use App\Models\Organization;

trait BelongsToOrganization
{
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}