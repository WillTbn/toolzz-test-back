<?php

namespace App\Services\Auth;

use App\Exceptions\ParametInvalidException;
use App\Services\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $this->passportClientId = $client_id;
        $this->passportClientSecret = $client_secret;
        $this->email = $email;
        $this->password = $password;
    }
    public function getPassportClientId():string
    {
        // trim( $this->passportClientId, '"')
        return $this->passportClientId;
    }
    public function getPassportClientSecret():string
    {
        //trim( $this->passportClientSecret, '"')
        return $this->passportClientSecret;
    }
    public function setToken()
    {
        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => $this->grantType,
            'client_id' => $this->getPassportClientId(),
            'client_secret' => $this->getPassportClientSecret(),
            'username' => $this->email,
            'password' =>   $this->password,
            'scope' => $this->scope,
        ]);
        if ($response->successful()) {
            $this->token['token'] = $response->json();
        } else {
            // Log de erro para depuração
            logger('Erro ao obter token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Falha ao obter o token: ' . $response->status());
        }
    }
    public function getToken():array
    {
        return $this->token;
    }
    /**
     * @return PassportAuthService
     * 
     * @throws 
     */
    public function execute():PassportAuthService
    {
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            throw new ParametInvalidException(message:'Email e/ou senha invalidos.');
            // return $this->error('Não autorizado!', 401);
        }
        $this->setToken();
        return $this;
    }
}
