<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message = 'Hello from Laravel!')
    {
        $this->message = $message;
    }

    // Channel yang akan digunakan
    public function broadcastOn()
    {
        return new Channel('test-channel');
    }

    // Data yang dikirim
    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
