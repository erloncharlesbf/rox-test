<?php

declare(strict_types=1);

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'file_url' => route('attachments.show', ['attachment' => $this->id]),
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'type' => $this->type,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
