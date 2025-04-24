<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test for user registration
     */
    public function test_user_can_register_successfully(): void
    {
        $email = $this->faker->unique()->safeEmail;
        $response = $this->postJson('/api/register', [
            'name'      => $this->faker->name,
            'email'     => $email,
            'password'  => $this->faker->password,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    /**
     * Test for login user
     */
    public function test_user_can_login_successfully(): void
    {
        $email = $this->faker->unique()->safeEmail;
        $password = $this->faker->password;

        $user = User::factory()->create([
            'name'      => $this->faker->name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password,
            'token_name' => 'test_device',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);

        $response->assertJsonFragment([
            'email' => $email,
        ]);

        $this->assertNotEmpty($response->json()['data']['token']);
    }

    /**
     * Test for invalid login
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($this->faker->password),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'status',
            'message',
            'errors',
        ]);
    }

    /**
     * Test for user cannot register
     */
    public function test_user_cannot_register_with_existing_email(): void
    {
        $existingUser = User::factory()->create();

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => $existingUser->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(500);  // Status para erro de validação
        $response->assertJsonStructure([
            'status',
            'message',
            'errors',
        ]);
    }

    /**
     * Test for user can refresh token
     */
    public function test_user_can_refresh_token_successfully(): void
    {
        $user = User::factory()->create();

        // Cria um token de refresh
        $refreshToken = $user->createToken('refresh', ['*'], now()->addDays(7))->plainTextToken;

        // Faz a requisição de refresh
        $response = $this->withHeader('Authorization', 'Bearer ' . $refreshToken)
                        ->postJson('/api/refresh');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
                'refresh_token',
            ]
        ]);

        $this->assertNotEmpty($response->json('data.token'));
        $this->assertNotEmpty($response->json('data.refresh_token'));
    }

    /**
     * Test for user cannot refresh token
     */
    public function test_refresh_fails_with_invalid_token(): void
    {
        $invalidToken = 'invalid.token.string';

        $response = $this->withHeader('Authorization', 'Bearer ' . $invalidToken)
                        ->postJson('/api/refresh');

        $response->assertStatus(401);

        $response->assertJsonFragment([
            'status' => 'error',
            'message' => 'Invalid or expired refresh token',
        ]);
    }
}
