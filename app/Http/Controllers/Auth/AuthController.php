<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Auth\CreateUserService;
use App\Services\Auth\PassportAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    /**
     * User registration
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $registerService = (new CreateUserService(
            $request->client_id,
            $request->client_secret,
            $request->email,
            $request->password,
            $request->name, 
            $request->username,
            $request->birthday)
            )->execute();
        return $this->success($registerService->getToken(), 'Usuário registrado com sucesso!', 201);
    }
    /**
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function auth(AuthRequest $request):JsonResponse
    {
        $passportService = (new PassportAuthService(
            $request->client_id,
            $request->client_secret,
            $request->email,
            $request->password )
        )->execute();

        return $this->success($passportService->getToken(), 'Usuário logado com sucesso!', 200);
        // return $response->json();
    }
    /**
     * Get user authenticate
     *
     * @param  AuthRequest  $request
     */
    public function validate(): JsonResponse
    {
        if(!Auth::check()){
            return $this->error('Usuário não autentificado!', 500);
        }
        return $this->success(['user' =>$this->loggedUser], 'Dados usuário logado!', 200);
    }
    /**
     * refresh token
     * @param RefreshTokenRequest $request
     * @return void
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' =>  $request->client_id,
            'client_secret' => $request->client_secret,
            'scope' => '',
        ]);
        return $this->success( ['user' => $response->json()], 'Token renovado!', 200);
    }
    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user =  $request->user();
        $user->tokens()->delete();
        return $this->success([], 'Usuário deslogado, com sucesso!', 204);
    }
}
