<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\CreateChatRequest;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
     /**
     * return JsonResponse
     */
    public function getAll():JsonResponse
    {
        $user =  $this->loggedUser;
        $chats = DB::table('chats')
            ->join('users as user_one', 'chats.user_one_id', '=', 'user_one.id')
            ->join('users as user_two', 'chats.user_two_id', '=', 'user_two.id')
            ->leftJoin(DB::raw('(SELECT chat_id, body, created_at FROM chat_messages WHERE id IN (SELECT MAX(id) FROM chat_messages GROUP BY chat_id)) as last_message'), 'chats.id', '=', 'last_message.chat_id')
            ->where(function($query) use ($user) {
                $query->where('chats.user_one_id',  $user->id)
                    ->orWhere('chats.user_two_id', $user->id);
            })
            ->select(
                'chats.hash_id',
                DB::raw("IF(chats.user_one_id = $user->id, user_two.name, user_one.name) as user_name"),
                DB::raw("IF(chats.user_one_id = $user->id, user_two.email, user_one.email) as user_email"),
                DB::raw("IF(chats.user_one_id = $user->id, user_two.photo, user_one.photo) as user_photo"),
                'last_message.body as last_message_body',
                'last_message.created_at as last_message_time'
            )
        ->get();
        // $chats = $user->chats()->with('latestMessage')->get();
        return new JsonResponse(
            [
                "message" => 'Seus chats!',
                "chat" => $chats
            ],
            200
        );
    }
    /**
     * @param CreateChatRequest $request
     * @return JsonResponse
     */
    public function createChat(CreateChatRequest $request):JsonResponse
    {
        // $user = Auth::user();
        $chat = new Chat();
        $chat->user_one_id = $this->loggedUser->id;
        $chat->user_two_id = $request->user_two_id;
        $chat->hash_id = md5(date('dmYHis'));
        $chat->saveOrFail();

        // $service = new CreateChatService($chat,  $this->loggedUser->id, $request->body);
        // $service->execute();
        return new JsonResponse(
            [
                "message" => 'Chat criado com sucesso!',
                "chat" => $chat
            ],
            200
        );

    }
}
