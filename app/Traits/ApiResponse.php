<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param String|Array|nullable|Collection $data content response
     * @param String $message
     * @param Number $code status code
     */
    protected function success($data, $message = 'Tudo certo!', $code= 200):JsonResponse
    {
        return response()->json([
            'sucess' => true,
            'message' => $message,
            ...$data
        ], $code);
    }
    /**
     * @param String|Array|nullable|Collection $data content response
     * @param String $message
     * @param Number $code status code
     */
    protected function error($message = 'Algo errado na solicitação!', $code= 400):JsonResponse
    {
        return response()->json([
            'sucess' => false,
            'message' => $message,
        ], $code);
    }
}
