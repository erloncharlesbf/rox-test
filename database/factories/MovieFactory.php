<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'director' => fake()->name(),
            'description' => fake()->paragraph(3),
            'studio' => fake()->company(),
            'release_date' => fake()->date(),
            'language' => fake()->randomElement(['en', 'pt', 'es', 'fr']),
            'duration' => fake()->numberBetween(80, 240),
            'genre' => fake()->randomElement(['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Thriller', 'Romance']),
            'rating' => fake()->randomFloat(1, 1, 10),
            'price' => fake()->randomFloat(2, 5, 50),
            'status' => fake()->randomElement(['available', 'unavailable', 'coming_soon']),
        ];
    }
}
