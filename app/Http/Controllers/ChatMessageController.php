<?php

namespace App\Http\Controllers;

use App\Events\SendMessageChat;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;

class ChatMessageController extends Controller
{
    /**
    * @param  Chat $ChatMessage
    * @param  SendMessageRequest $request
    * @return JsonResponse
     */
    public function store(Chat $chat, SendMessageRequest $request):JsonResponse
    {
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chat->id;
        $chatMessage->author_id = $this->loggedUser->id;
        $chatMessage->receiver_id = $request->receiver_id;
        $chatMessage->body = json_encode(['text' => $request->text]);
        $chatMessage->is_read = false;
        $chatMessage->saveOrFail();
        broadcast(new SendMessageChat($chatMessage));

        return $this->success(['chat_message' =>$chatMessage], 'Messagem enviada!');
    }
    /**
     * @param  Chat $ChatMessage
     */
    public function getAllChatMessage(Chat $chat):JsonResponse
    {
        if($chat->user_one_id != $this->loggedUser->id && $chat->user_two_id != $this->loggedUser->id)
        {
            $this->error('Você não tem acesso!', 401);
        }
        
        $header = $chat->otherUser($this->loggedUser->id, $chat->hash_id)->first();

        return $this->success([
            ///terei que pegar de outra forma para ter pagination
            'chat_messages' => $chat->chatMessages, 
            'header' =>$header
            ], 
            'Pegando as mensagens!');
    }
}
