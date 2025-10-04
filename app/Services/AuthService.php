<?php

namespace App\Services;

use App\Interface\UserRepositoryInterface;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService extends BaseService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        $data['password'] = bcrypt($data['password']);
        return $this->userRepository->create($data);
    }

    public function login(array $credentials): ?string
    {
        return JWTAuth::attempt($credentials) ?: null;
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function me(): User
    {
        return JWTAuth::user();
    }
}
