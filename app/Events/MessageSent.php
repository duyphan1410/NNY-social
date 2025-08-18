<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->message->receiver_id);
    }

    public function broadcastAs()
    {
        return 'message.received';
    }

    public function broadcastWith()
    {
        return [
            'message'  => $this->message,
            'sender'   => [
                'id'   => $this->message->sender->id,
                'name' => $this->message->sender->first_name . ' ' . $this->message->sender->last_name,
            ],
            'receiver' => [
                'id'   => $this->message->receiver->id,
                'name' => $this->message->receiver->first_name . ' ' . $this->message->receiver->last_name,
            ],
        ];
    }


}


