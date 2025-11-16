<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function __construct(private readonly \Illuminate\Database\Schema\Builder $builder) {}

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->builder->create('movies', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('director');
            $table->text('description')->nullable();
            $table->string('studio')->nullable();
            $table->date('release_date')->nullable();
            $table->string('language')->default('en');
            $table->integer('duration')->nullable(); // in minutes
            $table->string('genre')->nullable();
            $table->decimal('rating', 3, 1)->nullable(); // e.g., 8.5
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['available', 'unavailable', 'coming_soon'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering and searching
            $table->index('title');
            $table->index('director');
            $table->index('genre');
            $table->index('status');
            $table->index('release_date');
            $table->index('rating');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->builder->dropIfExists('movies');
    }
};
