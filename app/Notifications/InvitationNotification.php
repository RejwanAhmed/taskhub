<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invitation;
    /**
     * Create a new notification instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('invitations.show', ['token' => $this->invitation->token]);
        $organizationName = $this->invitation->organization->name;
        $inviterName = $this->invitation->inviter->name;
        $role = ucfirst($this->invitation->role);
        return (new MailMessage)
            ->subject("You've been invited to join {$organizationName}")
            ->view('emails.invite-user', [
                'acceptUrl' => $acceptUrl,
                'organizationName' => $organizationName,
                'inviterName' => $inviterName,
                'role' => $role,
            ]);     
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
