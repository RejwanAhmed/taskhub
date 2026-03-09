<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use App\Models\Organization;

interface InvitationRepositoryInterface
{
    public function createInvitation($validatedData);
    public function findPendingInvitation(Organization $currentOrganization, $email);
    public function updateInvitation(Invitation $invitation, $data);
}
