<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected $loggedUser;
    use ApiResponse;
    public function __construct()
    {
        $this->loggedUser = Auth::user();
    }
    
}
