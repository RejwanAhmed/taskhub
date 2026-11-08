<?php

namespace App\Events\Attachment;

use App\Models\Attachment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachmentCreated implements ShouldBroadcastNow

{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Attachment $attachment, public int $taskId)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */

    public function broadcastAs(): string
    {
        return 'AttachmentCreated';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tasks.' . $this->taskId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'attachment' => $this->attachment,
        ];
    }
}
