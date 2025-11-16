<?php

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('attachment belongs to attachable', function (): void {
    $movie = Movie::factory()->create();

    $attachment = \App\Models\Attachment::query()->create([
        'attachable_type' => Movie::class,
        'attachable_id' => $movie->id,
        'file_name' => 'test.jpg',
        'file_path' => 'path/to/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'type' => 'cover',
    ]);

    $this->assertInstanceOf(Movie::class, $attachment->attachable);
    $this->assertEquals($movie->id, $attachment->attachable->id);
});

it('attachment casts file size to integer', function (): void {
    $movie = Movie::factory()->create();

    $attachment = \App\Models\Attachment::query()->create([
        'attachable_type' => Movie::class,
        'attachable_id' => $movie->id,
        'file_name' => 'test.jpg',
        'file_path' => 'path/to/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => '2048',
        'type' => 'cover',
    ]);

    $this->assertIsInt($attachment->file_size);
    $this->assertEquals(2048, $attachment->file_size);
});
