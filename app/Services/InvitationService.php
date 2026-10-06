<?php 

namespace App\Services;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Models\User;
use App\Notifications\InvitationNotification;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InvitationService
{
    protected InvitationRepositoryInterface $invitationRepo;
    protected OrganizationRepositoryInterface $organizationRepo;
    protected UserRepositoryInterface $userRepo;

    public function __construct(InvitationRepositoryInterface $invitationRepo, OrganizationRepositoryInterface $organizationRepo, UserRepositoryInterface $userRepo)
    {
        $this->invitationRepo = $invitationRepo;
        $this->organizationRepo = $organizationRepo;
        $this->userRepo = $userRepo;
    }

    public function createInvitation(User $user, array $data, int $orgId): string
    {
        return DB::transaction(function () use ($user, $data, $orgId) {
            $organization = $this->organizationRepo->getCurrentOrganization($orgId);
            $email = $data['email'];
            $isMember = $this->organizationRepo->isMember($organization, $email);

            if ($isMember) 
                throw new BusinessException('User is already a member of this organization');

            $existingInvite = $this->invitationRepo->findPendingInvitation($organization, $email);

            $inviteData = [
                'token' => Str::random(32),
                'invited_by' => $user->id,
                'expires_at' => now()->addDays(7),
                'role' => $data['role'],
                'organization_id' => $orgId,
            ];

            if ($existingInvite) {
                $this->invitationRepo->updateInvitation($existingInvite, $inviteData);
                Notification::route('mail', $email)->notify(new InvitationNotification($existingInvite));
                return Constants::RESENT;
            }

            $data = array_merge($data, $inviteData);
            $invitation = $this->invitationRepo->createInvitation($data);

            Notification::route('mail', $email)->notify(new InvitationNotification($invitation));
            
            return Constants::SENT;
        });
    }

    public function getInvitation(string $token)
    {
        $invitation = $this->invitationRepo->getInvitation($token);

        if (!$invitation) 
            throw new BusinessException('Invitation not found');

        if ($invitation->accepted_at != null) 
            throw new BusinessException('Invitation already accepted');

        if ($invitation->expires_at < now())
            throw new BusinessException('Inviation has expired');
       
        return $invitation;
    }

    public function getInvitationDetails(string $token)
    {
        $invitation = $this->invitationRepo->getInvitation($token);        
        $hasAccount = $this->userRepo->checkUserExists($invitation->email);
        return [
            'invitation' => $invitation,
            'hasAccount' => $hasAccount,
        ];
    }

    public function acceptInvitation(User $user, string $token): int
    {
        return DB::transaction(function () use ($user, $token) {
            $invitation = $this->getInvitation($token);

            if (!$user) 
                throw new BusinessException('You must be logged in to accept the invitation');

            if ($user->email !== $invitation->email)
                throw new BusinessException('This invitation was sent to ' . $invitation->email . '. Please login with the correct account');

            $organization = $invitation->organization;
            $this->organizationRepo->attachUser($organization, $user->id, $invitation->role);
            $this->userRepo->updateCurrentOrganization($user, $organization->id);
            $this->invitationRepo->markInvitationAccepted($invitation);

            return $organization->id;
        });
    }

    public function registerInvitedUser(array $data, array $invitationDetails): array
    {
        return DB::transaction(function () use ($data, $invitationDetails) {
            $invitation = $invitationDetails['invitation'];
            $organization = $invitation->organization;

            if($data['email'] != $invitation->email) 
                throw new BusinessException('Registration email must match the invitation email: ' . $invitation->email);

            $user = $this->userRepo->createUser($invitation, $data);

            event(new Registered($user));
            $this->organizationRepo->attachUser($organization, $user->id, $invitation->role);
            $this->invitationRepo->markInvitationAccepted($invitation);

            return ['user' => $user, 'orgId' => $organization->id];
        });
    }

    public function loginInvitedUser(User $user, array $invitationDetails): int
    {
        $invitation = $invitationDetails['invitation'];
        if ($user->email !== $invitation->email) {
            throw new BusinessException(
                'This invitation was sent to ' . $invitation->email . '. You are logged in with ' . $user->email . '. Please log in with the correct account.'
            );
        }
        DB::transaction(function () use ($user, $invitation) {
            $this->organizationRepo->attachUser($invitation->organization, $user->id, $invitation->role);
            $this->invitationRepo->markInvitationAccepted($invitation);
            $this->userRepo->updateCurrentOrganization($user, $invitation->organization->id);
        });

        return $invitation->organization->id;
    }
}