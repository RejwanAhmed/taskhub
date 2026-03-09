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
}
