<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

#[Group('Authentication', 'APIs for user authentication, registration, and password management. These endpoints handle user access control and account security.')]
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService, private readonly \Illuminate\Auth\AuthManager $authManager, private readonly \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
    ) {}

    #[Endpoint(
        title: 'Register a new user',
        description: 'Create a new user account with email and password. Returns the user data and an authentication token.'
    )]
    #[BodyParam('name', 'string', 'The user\'s full name', required: true, example: 'John Doe')]
    #[BodyParam('email', 'string', 'The user\'s email address (must be unique)', required: true, example: 'john@example.com')]
    #[BodyParam('password', 'string', 'The password (minimum 8 characters)', required: true, example: 'password123')]
    #[BodyParam('password_confirmation', 'string', 'Password confirmation (must match password)', required: true, example: 'password123')]
    #[Response(['message' => 'User registered successfully', 'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'], 'token' => 'your-api-token'], status: 201)]
    #[Response(['message' => 'Validation failed', 'errors' => ['email' => ['This email is already registered.']]], status: 422)]
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->responseFactory->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
        ], 201);
    }

    #[Endpoint(
        title: 'Login user',
        description: 'Authenticate a user with email and password. Returns user data and an authentication token on success.'
    )]
    #[BodyParam('email', 'string', 'The user\'s email address', required: true, example: 'john@example.com')]
    #[BodyParam('password', 'string', 'The user\'s password', required: true, example: 'password123')]
    #[Response(['message' => 'Login successful', 'user' => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'], 'token' => 'your-api-token'], status: 200)]
    #[Response(['message' => 'Invalid credentials'], status: 401)]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (! $result) {
            return $this->responseFactory->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return $this->responseFactory->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
        ]);
    }

    #[Endpoint(
        title: 'Logout user',
        description: 'Revoke the current user\'s authentication token. Requires authentication.'
    )]
    #[Response(['message' => 'Logged out successfully'], status: 200)]
    #[Response(['message' => 'Unauthenticated'], status: 401)]
    public function logout(): JsonResponse
    {
        $this->authService->logout($this->authManager->user());

        return $this->responseFactory->json([
            'message' => 'Logged out successfully',
        ]);
    }

    #[Endpoint(
        title: 'Request password reset',
        description: 'Send a password reset link to the user\'s email address. The email will contain a token to reset the password.'
    )]
    #[BodyParam('email', 'string', 'The user\'s registered email address', required: true, example: 'john@example.com')]
    #[Response(['message' => 'Password reset link sent to your email'], status: 200)]
    #[Response(['message' => 'We could not find a user with that email address.'], status: 422)]
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink($request->input('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->responseFactory->json([
                'message' => 'Password reset link sent to your email',
            ]);
        }

        return $this->responseFactory->json([
            'message' => 'Unable to send password reset link',
        ], 422);
    }

    #[Endpoint(
        title: 'Reset password',
        description: 'Reset user password using the token received via email. The token is sent when requesting a password reset.'
    )]
    #[BodyParam('token', 'string', 'The password reset token from email', required: true, example: 'abc123xyz')]
    #[BodyParam('email', 'string', 'The user\'s email address', required: true, example: 'john@example.com')]
    #[BodyParam('password', 'string', 'The new password (minimum 8 characters)', required: true, example: 'newpassword123')]
    #[BodyParam('password_confirmation', 'string', 'Password confirmation (must match password)', required: true, example: 'newpassword123')]
    #[Response(['message' => 'Password has been reset successfully'], status: 200)]
    #[Response(['message' => 'Invalid or expired token'], status: 422)]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->responseFactory->json([
                'message' => 'Password has been reset successfully',
            ]);
        }

        return $this->responseFactory->json([
            'message' => 'Invalid or expired token',
        ], 422);
    }
}
