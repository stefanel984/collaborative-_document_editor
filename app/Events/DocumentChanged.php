<?php

namespace App\Events;

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

    public function __construct($documentId, DocumentChange $change)
    {
        $this->change = $change;
        $this->document = [
            'id' => $documentId,
            'operation' => $change->document->operation,
            'title' => $change->document->title,
            'version' => $change->version,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('document.' . $this->document['id']);
    }

    public function broadcastWith()
    {
        return [
            'document' => $this->document,
            'change' => $this->change,
        ];
    }
}