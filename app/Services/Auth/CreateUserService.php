<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
     * birthday
     * @var Date
     */
    private $birthday;
    /**
     * username
     * @var string
     */
    private $username;
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
    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $username
     * @param Date $birthday
     */
    public function __construct(
        $client_id, $client_secret, $email, 
        $password, $name, $username, $birthday
    )
    {
        $this->passportClientSecret = $client_secret;
        $this->passportClientId = $client_id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->username = $username;
        $this->birthday = Carbon::createFromFormat('d-m-Y', $birthday)->format('Y-m-d');
    }
    public function setUser()
    {
        $this->user = new User();
        $this->user->email = $this->email;
        $this->user->name = $this->name;
        $this->user->username = $this->username;
        $this->user->birthday = $this->birthday;
        $this->user->hash_id = md5($this->email.date('dmYHis'));
        $this->user->remember_token = substr(uniqid(rand()), 0, 5);
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
        $this->token['token'] = $response->json();
    }
    public function getToken():array
    {
        return $this->token;
    }
    public function sendVerifyEmail(){
        $this->getUser()->notify(new VerifyEmailNotification($this->getUser()->remember_token));
    }

    public function execute():CreateUserService
    {
        $this->setUser();
        $this->setToken();
        $this->sendVerifyEmail();
        return $this;
    }
}
