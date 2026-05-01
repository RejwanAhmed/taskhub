<?php 

namespace App\Services;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationNotification;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Core\BaseModelService;
use App\Support\OrganizationSession;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InvitationService extends BaseModelService
{
    protected InvitationRepositoryInterface $invitationRepo;
    protected OrganizationRepositoryInterface $organizationRepo;
    protected UserRepositoryInterface $userRepo;

    
    public function model(): string
    {
        return Invitation::class;
    }

    public function __construct(InvitationRepositoryInterface $invitationRepo, OrganizationRepositoryInterface $organizationRepo, UserRepositoryInterface $userRepo)
    {
        $this->invitationRepo = $invitationRepo;
        $this->organizationRepo = $organizationRepo;
        $this->userRepo = $userRepo;
    }

    public function createInvitation(User $user, $validatedData, $currentOrganizationId)
    {
        return DB::transaction(function () use ($user, $validatedData, $currentOrganizationId) {
            $currentOrganization = $this->organizationRepo->getCurrentOrganization($currentOrganizationId);
            $email = $validatedData['email'];
            $isMember = $this->organizationRepo->isMember($currentOrganization, $email);

            if ($isMember) 
                throw new BusinessException('User is already a member of this organization');

            $existingInvite = $this->invitationRepo->findPendingInvitation($currentOrganization, $email);

            $data = [
                'token' => Str::random(32),
                'invited_by' => $user->id,
                'expires_at' => now()->addDays(7),
                'role' => $validatedData['role'],
                'organization_id' => $currentOrganizationId,
            ];

            if ($existingInvite) {
                $this->invitationRepo->updateInvitation($existingInvite, $data);
                Notification::route('mail', $email)->notify(new InvitationNotification($existingInvite));
                return Constants::RESENT;
            }

            $validatedData = array_merge($validatedData, $data);
            $invitation = $this->invitationRepo->createInvitation($validatedData);

            Notification::route('mail', $email)->notify(new InvitationNotification($invitation));
            
            return Constants::SENT;
        });
    }

    public function getInvitation($token)
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

    public function getInvitationDetails($token)
    {
        $invitation = $this->invitationRepo->getInvitation($token);        
        $hasAccount = $this->userRepo->checkUserExists($invitation->email);
        return [
            'invitation' => $invitation,
            'hasAccount' => $hasAccount,
        ];
    }

    public function acceptInvitation($user, $token)
    {
        return DB::transaction(function () use ($user, $token) {
            $invitation = $this->getInvitation($token);

            if (!$user) 
                throw new BusinessException('You must be logged in to accept the invitation');

            if ($user->email !== $invitation->email)
                throw new BusinessException('This invitation was sent to ' . $invitation->email . '. Please login with the correct account');

            $organization = $invitation->organization;
            $this->organizationRepo->attachUser($organization, $user->id, $invitation->role);
            $this->userRepo->updateCurrentOrganzation($user, $organization->id);
            $this->invitationRepo->markInvitationAccepted($invitation);
            OrganizationSession::setCurrentOrg($organizationId);

            return true;
        });
    }

    public function registerInvitedUser($validatedData, $invitationDetails)
    {
        return DB::transaction(function () use ($validatedData, $invitationDetails) {
            $invitation = $invitationDetails['invitation'];
            $organization = $invitation->organization;

            if($validatedData['email'] != $invitation->email) 
                throw new BusinessException('Registration email must match the invitation email: ' . $invitation->email);

            $user = $this->userRepo->createUser($invitation, $validatedData);

            event(new Registered($user));
            $this->organizationRepo->attachUser($organization, $user->id, $invitation->role);
            $this->invitationRepo->markInvitationAccepted($invitation);
            Auth::login($user);
            OrganizationSession::setCurrentOrg($organization->id);
        });
    }

    public function loginInvitedUser($user, $invitationDetails)
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
            $this->userRepo->updateCurrentOrganzation($user, $invitation->organization->id);
            OrganizationSession::setCurrentOrg($organizationId);
        });

        return $invitation->organization->name;
    }
}