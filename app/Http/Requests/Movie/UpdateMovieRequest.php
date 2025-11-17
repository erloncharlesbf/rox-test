<?php

declare(strict_types=1);

namespace App\Http\Requests\Movie;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'director' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'studio' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'language' => ['nullable', 'string', 'max:10'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'genre' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', 'in:available,unavailable,coming_soon'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The movie title is required.',
            'director.required' => 'The director name is required.',
            'status.in' => 'The status must be one of: available, unavailable, coming_soon.',
            'rating.max' => 'The rating must not exceed 10.',
            'cover.image' => 'The cover must be an image file.',
            'cover.max' => 'The cover image size must not exceed 2MB.',
        ];
    }
}
