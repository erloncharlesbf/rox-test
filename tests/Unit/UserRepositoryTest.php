<?php

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->repository = new UserRepository(new User);
});

it('can find user by email', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $found = $this->repository->findByEmail('test@example.com');

    $this->assertNotNull($found);
    $this->assertEquals($user->id, $found->id);
});

it('find by email returns null for nonexistent', function (): void {
    $found = $this->repository->findByEmail('nonexistent@example.com');

    $this->assertNull($found);
});

it('can create user', function (): void {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ];

    $user = $this->repository->createUser($data);

    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals('Test User', $user->name);
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('can update password', function (): void {
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

    $newPassword = Hash::make('newpassword123');
    $result = $this->repository->updatePassword($user, $newPassword);

    $this->assertTrue($result);
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});
