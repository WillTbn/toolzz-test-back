<?php

namespace App\Services\ChatMessage;

use App\Events\SendMessageChat;
use App\Mail\ChatInitialMail;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\InitialChatNotification;
use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 *Send Chat
 *
 */
class SendInitialChatMessageService extends Service
{
    /**
     * chat
     *
     */
    private Chat $chat;
    /**
     * user receiver message
     */
    private User $receiver;
    /**
     * user send message
     */
    private User $sender;
     /**
     * customer the sender
     */
    private String $message;
     /**
      * chat message
      */
    private ChatMessage $chatMessage;

    public function __construct(Chat $chat,User $sender, User $receiver, String $message)
    {
        $this->chat = $chat;
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->message = $message;
    }
    /**
     * set menssage to Body chat
     */
    public function setChatMessage()
    {
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $this->chat->id;
        $chatMessage->author_id = $this->chat->user_one_id;
        $chatMessage->receiver_id = $this->chat->user_two_id;
        $chatMessage->body = json_encode(['text' => $this->getMessage()]);
        $chatMessage->is_read =false;
        $chatMessage->saveOrFail();

        $this->chatMessage = $chatMessage;
    }
    public function getChatMessage():ChatMessage
    {
        return $this->chatMessage;
    }
    public function getMessage():String
    {
        return $this->message;
    }
    public function getReceiver():User
    {
        return $this->receiver;
    }
    public function getSend():User
    {
        return $this->sender;
    }
    /**
     * send email initial chat
     */
    public function sendNotification()
    {
        // broadcast(new SendMessageChat($this->chat))->toOthers();
        $this->getReceiver()->notify(new InitialChatNotification($this->getSend(), $this->getMessage()));
    }
    /**
     * @return SendInitialChatMessageService
     */
    public function execute():SendInitialChatMessageService
    {
        // $this->setRecipient();
        $this->setChatMessage();

        $this->sendNotification();
        return $this;
    }
}
