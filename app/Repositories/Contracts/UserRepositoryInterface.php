<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use App\Models\User;

interface UserRepositoryInterface
{
    public function updateCurrentOrganization(User $user, int $orgId);
    public function createUser(Invitation $invitation, array $data);
    public function checkUserExists(string $email);
}
