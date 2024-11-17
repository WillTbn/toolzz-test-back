<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\PhotoUploadRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\User\UpdateUserPhotoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     /**
     * Create Account
     * @param  UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $birth = Carbon::createFromFormat('d-m-Y', $request->birthday)->format('Y-m-d');
        // return $this->success(['users' => $birth], 'Esses são todos o usuários!', 200);
        $userNew = User::where('id', $this->loggedUser->id)->first();
        $userNew->username = $request->username;
        $userNew->name = $request->name;
        $userNew->birthday = $birth;
        $userNew->UpdateOrFail();
        return $this->success(['user' => $userNew], 'Dados atualizados com sucesso!');
    }
    /**
     * Create Account
     * @param  PhotoUploadRequest $request
     * @return JsonResponse
     */
    public function updatePhoto(PhotoUploadRequest $request): JsonResponse
    {

        $service = new UpdateUserPhotoService($this->loggedUser, $request->photo);
        $service->execute();

        return $this->success(['user' => $service->getUserNew()], 'Foto atualizada com sucesso!');
    }
    /**
     * Create Account
     * @param  DeleteUserRequest $request
     * @return JsonResponse
     */
    public function destroy(DeleteUserRequest $request):JsonResponse
    {
        if(Hash::check($request->password, $this->loggedUser->password)){
            $user = User::where('hash_id', $this->loggedUser->hash_id)->first();
            $user->delete();
            return $this->success([], 'Que pena, nós vemos em breve!', 201);
        }
        return $this->error('Senha incorreta!', 402);
    }

}
