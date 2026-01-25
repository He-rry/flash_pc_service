<?php

namespace App\Http\Controllers\Traits;

trait ApiResponse
{
    protected function success($data = null, string $message = 'OK', int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}
