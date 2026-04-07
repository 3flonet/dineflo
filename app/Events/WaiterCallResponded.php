<?php

namespace App\Events;

use App\Models\WaiterCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WaiterCallResponded implements ShouldBroadcast
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
        // Public channel so guest customers can listen without authentication
        return [
            new Channel('restaurant-public.' . $this->waiterCall->restaurant_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'waiter.responded';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->waiterCall->id,
            'table_id' => $this->waiterCall->table_id,
            'status' => 'responded',
            'message' => "A waiter is on their way to Table {$this->waiterCall->table->name}!",
        ];
    }
}
