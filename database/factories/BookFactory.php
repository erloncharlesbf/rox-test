<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(3),
            'publisher' => fake()->company(),
            'publication_date' => fake()->date(),
            'language' => fake()->randomElement(['en', 'pt', 'es', 'fr']),
            'pages' => fake()->numberBetween(50, 1000),
            'genre' => fake()->randomElement(['Fiction', 'Non-Fiction', 'Science', 'Biography', 'Fantasy', 'Mystery', 'Romance']),
            'price' => fake()->randomFloat(2, 5, 100),
            'status' => fake()->randomElement(['available', 'unavailable', 'coming_soon']),
        ];
    }
}
