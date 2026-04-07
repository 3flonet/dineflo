<?php

namespace App\Events;

use App\Models\WaiterCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WaiterCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WaiterCall $waiterCall;

    /**
     * Create a new event instance.
     */
    public function __construct(WaiterCall $waiterCall)
    {
        $this->waiterCall = $waiterCall;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->waiterCall->restaurant_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'waiter.called';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->waiterCall->id,
            'table_id' => $this->waiterCall->table_id,
            'table_name' => $this->waiterCall->table->name,
            'table_area' => $this->waiterCall->table->area,
            'called_at' => ($this->waiterCall->called_at ?? now())->toIso8601String(),
            'message' => "Table {$this->waiterCall->table->name} is calling for assistance!",
        ];
    }
}
