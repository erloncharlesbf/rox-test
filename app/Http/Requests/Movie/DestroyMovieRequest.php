<?php

declare(strict_types=1);

namespace App\Http\Requests\Movie;

use Illuminate\Foundation\Http\FormRequest;

class DestroyMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [];
    }
}
