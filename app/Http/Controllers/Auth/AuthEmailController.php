<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailTokenRequest;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthEmailController extends Controller
{
    /**
     * @param VerifyEmailTokenRequest $request
     */
    public function verifyEmailToken(VerifyEmailTokenRequest $request)
    {
        $user = $request->user();
        $user->email_verified_at = Carbon::now();
        $user->UpdateOrFail();
        return $this->success(['user' => $user], 'Email verificado!', 200);
    }
    /**
     * @param Request $request
     */
    public function resendTokenEmail(Request $request)
    {
        $user = $request->user();
        $user->remember_token = substr(uniqid(rand()), 0, 5);;
        $user->UpdateOrFail();
        $user->notify(new VerifyEmailNotification($user->remember_token));
        return $this->success([], 'Reenviado o código para verificação!', 200);
    }
}
