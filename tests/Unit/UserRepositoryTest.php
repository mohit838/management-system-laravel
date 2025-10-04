<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository(new User());
    }

    public function test_can_create_user()
    {
        $user = $this->userRepository->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_can_find_user_by_email()
    {
        $user = User::factory()->create(['email' => 'findme@example.com']);

        $found = $this->userRepository->findByEmail('findme@example.com');

        $this->assertEquals($user->id, $found->id);
    }
}
