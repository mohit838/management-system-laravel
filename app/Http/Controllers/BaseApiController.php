<?php

namespace App\Http\Controllers;

use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    protected function respondSuccess(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($message, $data, $statusCode);
    }

    protected function respondError(string $message, int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $statusCode, $errors);
    }
}
