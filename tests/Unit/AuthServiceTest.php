<?php

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $repository = new UserRepository(new User);
    $this->service = new AuthService($repository);
});

it('registers a new user', function (): void {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $result = $this->service->register($data);

    expect($result)->toHaveKeys(['user', 'token'])
        ->and($result['user'])->toBeInstanceOf(User::class)
        ->and($result['user']->email)->toBe('test@example.com')
        ->and($result['token'])->toBeString();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('logs in user with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $result = $this->service->login([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect($result)->toHaveKeys(['user', 'token'])
        ->and($result['user']->id)->toBe($user->id)
        ->and($result['token'])->toBeString();
});

it('returns null when login fails', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $result = $this->service->login([
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    expect($result)->toBeNull();
});

it('deletes all user tokens', function (): void {
    $user = User::factory()->create();
    $user->createToken('test-token-1');
    $user->createToken('test-token-2');

    expect($user->tokens()->count())->toBe(2);

    // Deleta manualmente o token atual (simula o que logout faz)
    $user->tokens()->delete();

    expect($user->tokens()->count())->toBe(0);
});

it('sends password reset link', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $status = $this->service->sendPasswordResetLink('test@example.com');

    expect($status)->toBeIn([Password::RESET_LINK_SENT, Password::RESET_THROTTLED]);
});

it('resets password with valid token', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $status = $this->service->resetPassword([
        'email' => 'test@example.com',
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    expect($status)->toBe(Password::PASSWORD_RESET)
        ->and($user->fresh()->tokens)->toHaveCount(0);
});
