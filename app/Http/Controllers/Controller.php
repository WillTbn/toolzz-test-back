<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected $loggedUser;
    public function __construct()
    {
        $this->loggedUser = Auth::user();
    }
    use ApiResponse;
}
