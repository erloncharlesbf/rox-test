<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Attachment\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'publisher' => $this->publisher,
            'publication_date' => $this->publication_date?->format('Y-m-d'),
            'language' => $this->language,
            'pages' => $this->pages,
            'genre' => $this->genre,
            'price' => $this->price,
            'status' => $this->status,
            'cover' => new AttachmentResource($this->whenLoaded('cover')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
