<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_date',
        'language',
        'pages',
        'genre',
        'price',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Attachment, $this>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<\App\Models\Attachment, $this>
     */
    public function cover(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable')
            ->where('type', 'cover');
    }

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'pages' => 'integer',
            'price' => 'decimal:2',
        ];
    }
}
