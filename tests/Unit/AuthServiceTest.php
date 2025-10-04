<?php

namespace Tests\Unit;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind UserRepository to interface
        $this->app->bind(UserRepositoryInterface::class, function () {
            return new UserRepository(new User());
        });

        $this->authService = $this->app->make(AuthService::class);
    }

    public function test_can_register_user()
    {
        $user = $this->authService->register([
            'name' => 'Service User',
            'email' => 'service@example.com',
            'password' => 'password123',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('service@example.com', $user->email);
    }

    public function test_can_login_user()
    {
        $user = $this->authService->register([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $token = $this->authService->login([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $this->assertNotNull($token);
        $this->assertIsString($token);
    }

    public function test_can_get_authenticated_user()
    {
        $user = $this->authService->register([
            'name' => 'Me User',
            'email' => 'me@example.com',
            'password' => 'password123',
        ]);

        $token = $this->authService->login([
            'email' => 'me@example.com',
            'password' => 'password123',
        ]);

        JWTAuth::setToken($token);
        $me = $this->authService->me();

        $this->assertEquals($user->email, $me->email);
    }
}
