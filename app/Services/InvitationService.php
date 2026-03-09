<?php 

namespace App\Services;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationNotification;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Services\Core\BaseModelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InvitationService extends BaseModelService
{
    protected $invitationRepo;
    protected $organizationRepo;
    
    public function model(): string
    {
        return Invitation::class;
    }

    public function __construct(InvitationRepositoryInterface $invitationRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->invitationRepo = $invitationRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function createInvitation(User $user, $validatedData)
    {
        return DB::transaction(function () use ($user, $validatedData) {
            $currentOrganization = $user->currentOrganization;
            $email = $validatedData['email'];
            $isMember = $this->organizationRepo->isMember($currentOrganization, $email);

            if ($isMember) 
                throw new BusinessException('User is already a member of this organization');

            $existingInvite = $this->invitationRepo->findPendingInvitation($currentOrganization, $email);

            $data = [
                'token' => Str::random(32),
                'invited_by' => $user->id,
                'expires_at' => now()->addDays(7),
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
}