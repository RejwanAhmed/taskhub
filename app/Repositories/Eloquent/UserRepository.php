<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function updateCurrentOrganzation(User $user, $organizationId)
    {
        $user->update(['current_organization_id' => $organizationId]);
    }

    public function createUser(Invitation $invitation, $validatedData)
    {
        return User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'current_organization_id' => $invitation->organization_id,
        ]);
    }
}
