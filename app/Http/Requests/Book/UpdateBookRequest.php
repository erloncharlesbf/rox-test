<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * @param  array  $query  The GET parameters
     * @param  array  $request  The POST parameters
     * @param  array  $attributes  The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param  array  $cookies  The COOKIE parameters
     * @param  array  $files  The FILES parameters
     * @param  array  $server  The SERVER parameters
     * @param  string|resource|null  $content  The raw body data
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null, private readonly ?\Illuminate\Contracts\Auth\Guard $guard = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function authorize(): bool
    {
        return $this->guard->check();
    }

    public function rules(): array
    {
        $bookId = $this->route('book');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'isbn' => ['sometimes', 'required', 'string', "unique:books,isbn,{$bookId}"],
            'description' => ['nullable', 'string'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date'],
            'language' => ['nullable', 'string', 'max:10'],
            'pages' => ['nullable', 'integer', 'min:1'],
            'genre' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', 'in:available,unavailable,coming_soon'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The book title is required.',
            'author.required' => 'The author name is required.',
            'isbn.required' => 'The ISBN is required.',
            'isbn.unique' => 'This ISBN already exists in the database.',
            'status.in' => 'The status must be one of: available, unavailable, coming_soon.',
            'cover.image' => 'The cover must be an image file.',
            'cover.max' => 'The cover image size must not exceed 2MB.',
        ];
    }
}
