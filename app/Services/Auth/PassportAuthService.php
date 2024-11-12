<?php

namespace App\Services\Auth;

use App\Services\Service;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PassportAuthService extends Service
{
    use ApiResponse;
    /**
     * grant_type
     * @var string
     */
    private $grantType = 'password';
    /**
     * PASSPORT_CLIENT_ID
     * @var string
     */
    private string $passportClientId;
    /**
     * PASSPORT_CLIENT_SECRET
     * @var string
     */
    private string $passportClientSecret;
    /**
     * email
     * @var string
     */
    private $email;
    /**
     * password
     * @var string
     */
    private $password;
    /**
     * scope
     * @var string
     */
    private $scope = '';
     /**
     * token
     * @var json|array
     */
    private $token;
    public function __construct($client_id, $client_secret, $email, $password)
    {
        $this->passportClientSecret = $client_secret;
        $this->passportClientId = $client_id;
        $this->email = $email;
        $this->password = $password;
    }
    public function setToken()
    {
        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => $this->grantType,
            'client_id' => trim( $this->passportClientId, '"'),
            'client_secret' => trim( $this->passportClientSecret, '"'),
            'username' => $this->email,
            'password' =>   $this->password,
            'scope' => $this->scope,
        ]);
        // $user['token'] = $response->json();
        $this->token['token'] = $response->json();
    }
    public function getToken():array
    {
        return $this->token;
    }

    public function execute():PassportAuthService
    {
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            return $this->error('Não autorizado!', 401);
        }
        $this->setToken();
        return $this;
    }
}
