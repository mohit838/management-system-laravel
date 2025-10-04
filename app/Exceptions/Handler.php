<?php

namespace App\Exceptions;

use App\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {

            // Validation errors
            if ($e instanceof ValidationException) {
                return ApiResponse::error('Validation failed', 422, $e->errors());
            }

            // JWT errors
            if ($e instanceof TokenExpiredException) {
                return ApiResponse::error('Token has expired', 401);
            }
            if ($e instanceof TokenInvalidException) {
                return ApiResponse::error('Token is invalid', 401);
            }
            if ($e instanceof TokenBlacklistedException) {
                return ApiResponse::error('Token is blacklisted', 401);
            }
            if ($e instanceof JWTException) {
                return ApiResponse::error('Token error', 401, $e->getMessage());
            }

            // Auth errors
            if ($e instanceof AuthenticationException) {
                return ApiResponse::error('Unauthenticated', 401);
            }

            // Model not found
            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error('Resource not found', 404);
            }

            // Route not found
            if ($e instanceof NotFoundHttpException) {
                return ApiResponse::error('Endpoint not found', 404);
            }

            // Any other unhandled error
            return ApiResponse::error(
                'Server error',
                500,
                config('app.debug') ? $e->getMessage() : null
            );
        }

        return parent::render($request, $e);
    }
}
