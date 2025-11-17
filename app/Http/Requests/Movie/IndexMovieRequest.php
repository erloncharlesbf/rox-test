<?php

declare(strict_types=1);

namespace App\Http\Requests\Movie;

use Illuminate\Foundation\Http\FormRequest;

class IndexMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'director' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:available,unavailable,coming_soon'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.max' => 'The maximum items per page is 100.',
            'status.in' => 'The status must be one of: available, unavailable, coming_soon.',
        ];
    }
}
