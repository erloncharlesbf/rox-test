<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiValidationException extends Exception
{
    protected $code = 422;

    public function __construct(
        protected array $errors,
        string $message = 'Validation failed'
    ) {
        parent::__construct($message, $this->code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
