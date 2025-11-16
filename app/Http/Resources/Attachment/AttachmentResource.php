<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     */
    public function __construct($resource, private readonly \Illuminate\Routing\UrlGenerator $urlGenerator)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'file_url' => $this->urlGenerator->asset('storage/'.$this->file_path),
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'type' => $this->type,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
