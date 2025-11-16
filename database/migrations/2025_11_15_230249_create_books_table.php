<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('language')->default('en');
            $table->integer('pages')->nullable();
            $table->string('genre')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['available', 'unavailable', 'coming_soon'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering and searching
            $table->index('title');
            $table->index('author');
            $table->index('isbn');
            $table->index('genre');
            $table->index('status');
            $table->index('publication_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
