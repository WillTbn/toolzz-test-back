<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageChat
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public ChatMessage $chatMessage;
    /**
     * Create a new event instance.
     */
    public function __construct(
        ChatMessage $chatMessage
    )
    {
        $this->chatMessage = $chatMessage;
    }
    public function broadcastWith()
    {
        $user = User::where('id', $this->chatMessage->author_id)->first();
        $this->chatMessage->photo = $user->photo;
        $this->chatMessage->name = $user->name;
        return [
            'message' => $this->chatMessage,
            'photo' => $user->photo,
            'name' => $user->name,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->chatMessage->receiver_id),
        ];
    }
}
