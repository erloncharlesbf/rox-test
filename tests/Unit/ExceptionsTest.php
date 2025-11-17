<?php

use App\Exceptions\ApiValidationException;
use App\Exceptions\EndpointNotFoundException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthenticatedException;

it('throws UnauthenticatedException with default message', function (): void {
    $exception = new UnauthenticatedException;

    expect($exception->getMessage())->toBe('Unauthenticated');
    expect($exception->getCode())->toBe(401);
});

it('throws UnauthenticatedException with custom message', function (): void {
    $exception = new UnauthenticatedException('Custom auth error');

    expect($exception->getMessage())->toBe('Custom auth error');
    expect($exception->getCode())->toBe(401);
});

it('renders UnauthenticatedException as JSON', function (): void {
    $exception = new UnauthenticatedException;
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(401);
    expect($response->getData(true))->toBe(['message' => 'Unauthenticated']);
});

it('throws ResourceNotFoundException with default message', function (): void {
    $exception = new ResourceNotFoundException;

    expect($exception->getMessage())->toBe('Resource not found');
    expect($exception->getCode())->toBe(404);
});

it('throws ResourceNotFoundException with custom message', function (): void {
    $exception = new ResourceNotFoundException('Book not found');

    expect($exception->getMessage())->toBe('Book not found');
    expect($exception->getCode())->toBe(404);
});

it('renders ResourceNotFoundException as JSON', function (): void {
    $exception = new ResourceNotFoundException('Book not found');
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(404);
    expect($response->getData(true))->toBe(['message' => 'Book not found']);
});

it('throws EndpointNotFoundException with default message', function (): void {
    $exception = new EndpointNotFoundException;

    expect($exception->getMessage())->toBe('Endpoint not found');
    expect($exception->getCode())->toBe(404);
});

it('throws EndpointNotFoundException with custom message', function (): void {
    $exception = new EndpointNotFoundException('API route does not exist');

    expect($exception->getMessage())->toBe('API route does not exist');
    expect($exception->getCode())->toBe(404);
});

it('renders EndpointNotFoundException as JSON', function (): void {
    $exception = new EndpointNotFoundException;
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(404);
    expect($response->getData(true))->toBe(['message' => 'Endpoint not found']);
});

it('throws ApiValidationException with errors', function (): void {
    $errors = ['email' => ['The email field is required.']];
    $exception = new ApiValidationException($errors);

    expect($exception->getMessage())->toBe('Validation failed');
    expect($exception->getCode())->toBe(422);
    expect($exception->getErrors())->toBe($errors);
});

it('throws ApiValidationException with custom message', function (): void {
    $errors = ['email' => ['Invalid email format.']];
    $exception = new ApiValidationException($errors, 'Custom validation error');

    expect($exception->getMessage())->toBe('Custom validation error');
    expect($exception->getCode())->toBe(422);
});

it('renders ApiValidationException as JSON', function (): void {
    $errors = ['email' => ['The email field is required.']];
    $exception = new ApiValidationException($errors);
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true))->toBe([
        'message' => 'Validation failed',
        'errors' => $errors,
    ]);
});
