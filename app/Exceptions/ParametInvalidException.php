<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;

class ParametInvalidException extends Exception implements ShouldntReport
{
    public function render($request)
    {
        return response()->json([
            'error' => $this->message ? $this->message : 'Verifique os parâmetros passados.',
        ], 400);
    }
}
