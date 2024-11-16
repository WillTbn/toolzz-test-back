<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    /**
     * Get all Users
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        $users = User::whereNot('id', $this->loggedUser->id)
            ->where('username', 'LIKE', '%'.$request['username'].'%')
            ->paginate(3)->appends($request->query());;

        return $this->success(['users' => $users], 'Esses são todos o usuários!', 200);
    }
}
