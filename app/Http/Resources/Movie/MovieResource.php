<?php

declare(strict_types=1);

namespace App\Http\Resources\Movie;

use App\Http\Resources\Attachment\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'director' => $this->director,
            'description' => $this->description,
            'studio' => $this->studio,
            'release_date' => $this->release_date?->format('Y-m-d'),
            'language' => $this->language,
            'duration' => $this->duration,
            'genre' => $this->genre,
            'rating' => $this->rating,
            'price' => $this->price,
            'status' => $this->status,
            'cover' => AttachmentResource::make($this->whenLoaded('cover')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
