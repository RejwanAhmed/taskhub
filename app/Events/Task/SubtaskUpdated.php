<?php

namespace App\Events\Task;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubtaskUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Task $subtask, public int $parentTaskId)
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
        return 'SubtaskUpdated';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tasks.' . $this->parentTaskId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'subtask' => $this->subtask
        ];
    }
}
