<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'director',
        'description',
        'studio',
        'release_date',
        'language',
        'duration',
        'genre',
        'rating',
        'price',
        'status',
    ];

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function cover(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable')
            ->where('type', 'cover');
    }
    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'duration' => 'integer',
            'rating' => 'decimal:1',
            'price' => 'decimal:2',
        ];
    }
}
