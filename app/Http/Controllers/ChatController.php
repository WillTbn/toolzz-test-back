<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\CreateChatRequest;
use App\Models\Chat;
use App\Models\User;
use App\Services\Chat\CreateChatService;
use App\Services\ChatMessage\SendInitialChatMessageService;
use Illuminate\Http\JsonResponse;
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
        return $this->success(['chat' => $chats], 'Seus chats!');
    }
    /**
     * @param CreateChatRequest $request
     * @return JsonResponse
     */
    public function createChat(CreateChatRequest $request):JsonResponse
    {
        $send = $this->loggedUser->id;
        $receiver = User::where('hash_id', $request->user_hash_id)->first()->id;
        $exists = Chat::where(function($query) use ($send, $receiver) {
            $query->where('user_one_id', $send)
                  ->where('user_two_id', $receiver);
        })
        ->orWhere(function($query) use ($send, $receiver) {
            $query->where('user_one_id', $receiver)
                  ->where('user_two_id', $send);
        })
        ->exists();
        if($exists){
            return $this->error('Já existe vinculo, procure nas suas mensagens', 402);
        }
        // $user = Auth::user();
        $serviceCreateChat = (new CreateChatService($request->user_hash_id, $this->loggedUser))->execute();
        $serviceMessage = (new SendInitialChatMessageService(
            $serviceCreateChat->getChat(),
            $this->loggedUser,
            $serviceCreateChat->getReceiver(),
            $request->body
        ))->execute();
        return $this->success(['chat_message' => $serviceMessage->getMessage()], 'Chat criado com sucesso', 200);

    }
}
