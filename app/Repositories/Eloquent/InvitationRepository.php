<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\Contracts\InvitationRepositoryInterface;

class InvitationRepository implements InvitationRepositoryInterface
{
    protected $model;

    public function __construct(Invitation $model)
    {
        $this->model = $model;
    }

    public function createInvitation($validatedData)
    {
        return $this->model::create($validatedData);
    }

    public function findPendingInvitation(Organization $organization, $email)
    {
        return $organization->invitations()->where('email', $email)->whereNull('accepted_at')->first();
    }

    public function updateInvitation(Invitation $invitation, $data)
    {
        return $invitation->update($data);
    }

    public function getInvitation($token)
    {
        return $this->model::with(['organization', 'inviter'])->where('token', $token)->first();
    }

    public function checkUserExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function markInvitationAccepted(Invitation $invitation)
    {
        $invitation->update(['accepted_at' => now()]);
    }
}
