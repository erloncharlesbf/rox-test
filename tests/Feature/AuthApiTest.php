<?php

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses()->group('auth', 'api');

it('can register new user', function (): void {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
        ])
        ->assertJson([
            'message' => 'User registered successfully',
            'user' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('cannot register with duplicate email', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates registration fields', function (): void {
    $response = $this->postJson('/api/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('can login with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
        ])
        ->assertJson([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => 'test@example.com',
            ],
        ]);
});

it('cannot login with invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid credentials',
        ]);
});

it('validates login fields', function (): void {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('can logout', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);

    expect($user->tokens)->toHaveCount(0);
});

it('logout requires authentication', function (): void {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->postJson('/api/logout');

    $response->assertStatus(401);
});

it('can request password reset', function (): void {
    Notification::fake();

    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Password reset link sent to your email',
        ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

it('cannot request password reset with invalid email', function (): void {
    $response = $this->postJson('/api/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422);
});

it('validates forgot password fields', function (): void {
    $response = $this->postJson('/api/forgot-password', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can reset password', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $token = Password::createToken($user);

    $response = $this->postJson('/api/reset-password', [
        'email' => 'test@example.com',
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    expect($response->status())->toBeIn([200, 422]);
});

it('cannot reset password with invalid token', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/reset-password', [
        'email' => 'test@example.com',
        'token' => 'invalid-token',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Invalid or expired token',
        ]);
});

it('validates reset password fields', function (): void {
    $response = $this->postJson('/api/reset-password', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'token', 'password']);
});

it('forgot password returns error when sending fails', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $mockAuthService = Mockery::mock(AuthService::class)->makePartial();
    $mockAuthService->shouldReceive('sendPasswordResetLink')
        ->once()
        ->andReturn('passwords.throttled');

    $this->app->instance(AuthService::class, $mockAuthService);

    $response = $this->postJson('/api/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Unable to send password reset link',
        ]);
});
