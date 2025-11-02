<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $documentId;
    public $change;

    public function __construct($documentId, $change)
    {
        $this->documentId = $documentId;
        $this->change = $change;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('documents.' . $this->documentId);
    }

    public function broadcastWith()
    {
        return [
            'change' => $this->change
        ];
    }
}
