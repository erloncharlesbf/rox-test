<?php

use App\Models\Attachment;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('attachments', 'api');

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can display attachment file', function (): void {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('test.jpg');
    $book = Book::factory()->create();

    $attachment = Attachment::create([
        'file_name' => $file->getClientOriginalName(),
        'file_path' => $file->store('attachments', 'public'),
        'mime_type' => $file->getMimeType(),
        'file_size' => $file->getSize(),
        'type' => 'cover',
        'attachable_type' => Book::class,
        'attachable_id' => $book->id,
    ]);

    $response = $this->get("/api/attachments/{$attachment->id}");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
});

it('returns 404 when attachment file does not exist', function (): void {
    $book = Book::factory()->create();

    $attachment = Attachment::create([
        'file_name' => 'nonexistent.jpg',
        'file_path' => 'attachments/nonexistent.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'type' => 'cover',
        'attachable_type' => Book::class,
        'attachable_id' => $book->id,
    ]);

    $response = $this->get("/api/attachments/{$attachment->id}");

    $response->assertStatus(404);
});

it('returns 404 when attachment does not exist', function (): void {
    $response = $this->get('/api/attachments/99999');

    $response->assertStatus(404);
});

