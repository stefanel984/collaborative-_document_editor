<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentChange;

class DocumentChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $document;
    public $change;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($documentId, DocumentChange $change)
    {
        $this->change = $change;

        // Include full document content
        $this->document = [
            'id' => $documentId,
            'title' => $change->document->title,
            'content' => $change->document->content, // make sure content is up-to-date
            'version' => $change->version,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('document.' . $this->document['id']);
    }

    public function broadcastWith()
    {
        return [
            'document' => $this->document,
            'change' => $this->change,
        ];
    }
}