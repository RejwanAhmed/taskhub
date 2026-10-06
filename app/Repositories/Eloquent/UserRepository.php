<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;
    
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function updateCurrentOrganization(User $user, int $orgId)
    {
        $user->update(['current_organization_id' => $orgId]);
    }

    public function createUser(Invitation $invitation, array $data)
    {
        return $this->model::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'current_organization_id' => $invitation->organization_id,
        ]);
    }

    public function checkUserExists(string $email)
    {
        return $this->model::where('email', $email)->exists();
    }
}
