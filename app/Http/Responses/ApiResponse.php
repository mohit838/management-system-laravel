<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function success(
        string $message,
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success'    => true,
            'statusCode' => $statusCode,
            'message'    => $message,
            'data'       => $data,
        ], $statusCode);
    }

    public static function error(
        string $message,
        int $statusCode = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'success'    => false,
            'statusCode' => $statusCode,
            'message'    => $message,
            'errors'     => $errors,
        ], $statusCode);
    }
}
