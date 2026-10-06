<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use App\Models\Organization;

interface InvitationRepositoryInterface
{
    public function createInvitation(array $data);
    public function findPendingInvitation(Organization $organization, string $email);
    public function updateInvitation(Invitation $invitation, array $data);
    public function getInvitation(string $token);
    public function markInvitationAccepted(Invitation $invitation);
}
