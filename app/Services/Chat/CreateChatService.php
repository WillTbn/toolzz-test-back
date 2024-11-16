<?php

namespace App\Services\Chat;

use App\Models\Chat;
use App\Models\User;
use App\Services\Service;

/**
 *Send Chat
 *
 */
class CreateChatService extends Service
{

    /**
     * user receiver hash_id
     * @param string
     */
    private string $receiver_hash_id;
    /**
     * user receiver hash_id
     * @param User
     */
    private User $receiver;

    /**
     * user send hash_id
     * @param User
     */
    private User $send_hash_id;
    /**
     * chat
     * @param Chat
     */
    private Chat $chat;
    /**
     * @param String $receiver_hash_id User receiver message chat user_two_id
     * @param String $send_hash_id User send message chat user_one_id
     */
    public function __construct( string $receiver_hash_id, User $send_hash_id)
    {
        $this->receiver_hash_id = $receiver_hash_id;
        $this->send_hash_id = $send_hash_id;
    }

    public function setReceiver() 
    {
        $this->receiver = User::where('hash_id', $this->receiver_hash_id)->first();
    }

    public function getReceiver():User
    {
        return $this->receiver;
    }
    public function getSendUser():User
    {
        return $this->send_hash_id;
    }
    public function setChat()
    {
        $this->chat = new Chat();
        $this->chat->user_one_id = $this->getSendUser()->id;
        $this->chat->user_two_id = $this->getReceiver()->id;
        $this->chat->hash_id = md5(date('dmYHis'));
        $this->chat->saveOrFail();
    }
    public function getChat():Chat
    {
        return $this->chat;
    }
    /**
     * @return CreateChatService
     */
    public function execute():CreateChatService
    {
        $this->setReceiver();
        $this->setChat();

        return $this;
    }

}