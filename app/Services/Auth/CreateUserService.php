<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CreateUserService extends Service
{

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
     * name
     * @var string
     */
    private $name;
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

    /**
     * User
     * @var User
     */
    private $user;

    public function __construct($client_id, $client_secret, $email, $password, $name)
    {
        $this->passportClientSecret = $client_secret;
        $this->passportClientId = $client_id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }
    public function setUser()
    {
        $this->user = new User();
        $this->user->email = $this->email;
        $this->user->name = $this->name;
        $this->user->password = Hash::make($this->password);
        $this->user->saveOrFail();
    }

    public function getUser():User
    {
        return $this->user;
    }

    public function setToken()
    {
        $response = Http::post(env('APP_URL') . '/oauth/token', [
            'grant_type' => $this->grantType,
            'client_id' => trim( $this->passportClientId, '"'),
            'client_secret' => trim( $this->passportClientSecret, '"'),
            'username' => $this->email,
            'password' =>   $this->password,
            'scope' => $this->scope,
        ]);
        // $response = Http::post(env('APP_URL') . '/oauth/token', [
        //     'grant_type' => 'password',
        //     'client_id' => env('PASSPORT_CLIENT_ID'),
        //     'client_secret' => env('PASSPORT_CLIENT_SECRET'),
        //     'username' => $userData['email'],
        //     'password' => $userData['password'],
        //     'scope' => '',
        // ]);
        $this->token['token'] = $response->json();
    }
    public function getToken():array
    {
        return $this->token;
    }

    public function execute():CreateUserService
    {
        $this->setUser();
        $this->setToken();
        return $this;
    }
}
