<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository, private readonly \Illuminate\Database\DatabaseManager $databaseManager, private readonly \Illuminate\Contracts\Hashing\Hasher $hasher, private readonly \Illuminate\Auth\AuthManager $authManager, private readonly \Illuminate\Auth\Passwords\PasswordBrokerManager $passwordBrokerManager
    ) {}

    public function register(array $data): array
    {
        return $this->databaseManager->transaction(function () use ($data): array {
            $user = $this->userRepository->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $this->hasher->make($data['password']),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function login(array $credentials): ?array
    {
        if (! $this->authManager->attempt($credentials)) {
            return null;
        }

        $user = $this->authManager->user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function sendPasswordResetLink(string $email): string
    {
        return $this->passwordBrokerManager->sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data): string
    {
        return $this->passwordBrokerManager->reset($data, function (\App\Models\User $user, $password): void {
            $this->userRepository->updatePassword($user, $this->hasher->make($password));
            $user->tokens()->delete();
        });
    }
}
