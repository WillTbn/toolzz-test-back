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
        // $user = Auth::user();
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chat->id;
        $chatMessage->author_id = $this->loggedUser;
        $chatMessage->receiver_id = $request->receiver_id;
        $chatMessage->body = json_encode(['text' => $request->text]);
        $chatMessage->is_read = false;
        $chatMessage->saveOrFail();
        broadcast(new SendMessageChat($chatMessage));
        return new JsonResponse(
            [
                "message" => 'Mensagem enviada!',
                "chat_message" => $chatMessage
            ],
            200
        );
    }
    /**
     * @param  Chat $ChatMessage
     */
    public function getAllChatMessage(Chat $chat):JsonResponse
    {
        if($chat->user_one_id != $this->loggedUser->id && $chat->user_two_id != $this->loggedUser->id)
        {
            return new JsonResponse(
                [
                    "message" => 'Você naõ tem acesso!',
                    "chat_messages" => []
                ],
                401
            );
        }
        $header = $chat->otherUser($this->loggedUser->id, $chat->hash_id)->first();
        return new JsonResponse(
            [
                "message" => 'Pegando as mensagens!',
                'header' => $header,
                "chat_messages" => $chat->chatMessages
            ],
            200
        );
    }
}
