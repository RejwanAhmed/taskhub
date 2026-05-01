<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\OrganizationSession;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;
    
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function updateCurrentOrganzation(User $user, $organizationId)
    {
        $user->update(['current_organization_id' => $organizationId]);
        OrganizationSession::setCurrentOrg($organizationId);
    }

    public function createUser(Invitation $invitation, $validatedData)
    {
        return $this->model::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'current_organization_id' => $invitation->organization_id,
        ]);
    }

    public function checkUserExists($email)
    {
        return $this->model::where('email', $email)->exists();
    }
}
