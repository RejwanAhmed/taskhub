<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\Organization;
use App\Repositories\Contracts\InvitationRepositoryInterface;

class InvitationRepository implements InvitationRepositoryInterface
{
    protected $model;

    public function __construct(Invitation $model)
    {
        $this->model = $model;
    }

    public function createInvitation(array $data)
    {
        return $this->model::create($data);
    }

    public function findPendingInvitation(Organization $organization, string $email)
    {
        return $organization->invitations()->where('email', $email)->whereNull('accepted_at')->first();
    }

    public function updateInvitation(Invitation $invitation, array $data)
    {
        return $invitation->update($data);
    }

    public function getInvitation(string $token)
    {
        return $this->model::with(['organization', 'inviter'])->where('token', $token)->first();
    }

    public function markInvitationAccepted(Invitation $invitation)
    {
        $invitation->update(['accepted_at' => now()]);
    }
}
