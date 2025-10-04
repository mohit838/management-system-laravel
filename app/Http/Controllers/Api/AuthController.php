<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        return $this->respondSuccess('User registered successfully', new UserResource($user), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        if (!$token) {
            return $this->respondError('Invalid credentials', 401);
        }

        return $this->respondSuccess('Login successful', $this->respondWithToken($token));
    }

    public function me(): JsonResponse
    {
        return $this->respondSuccess('Authenticated user retrieved', new UserResource($this->authService->me()));
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->respondSuccess('Successfully logged out');
    }

    public function refresh(): JsonResponse
    {
        return $this->respondSuccess('Token refreshed', $this->respondWithToken($this->authService->refresh()));
    }

    private function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JwtAuth::factory()->getTTL() * 60,
        ];
    }
}
