<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'type',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }
}
