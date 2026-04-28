<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use App\Models\User;

interface UserRepositoryInterface
{
    public function updateCurrentOrganzation(User $user, $organizationId);
    public function createUser(Invitation $invitation, $validatedData);
}
