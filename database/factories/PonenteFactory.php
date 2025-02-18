<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ponente>
 */
class PonenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'fotografia' => fake()->imageUrl(200, 200, 'people'),
            'area_experiencia' => fake()->sentence(3),
            'red_social' => fake()->url(),
        ];
    }
}
