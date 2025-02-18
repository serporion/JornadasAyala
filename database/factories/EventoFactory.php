<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evento>
 */
class EventoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //Factoria no tratada como tal, cambiando fecha y hora y ejecutando db:seed cada vez.
        return [
            'tipo' => fake()->randomElement(['conferencia','taller']),
            'nombre' => fake()->randomElement([
                'Desarrollo de Videojuegos con Unity',
                'Introducción a Unreal Engine',
                'Diseño de Niveles',
                'Programación en C++ para Games',
                'Arte Digital en Videojuegos',
                'Blender Profesional'
            ]),
            'descripcion' => fake()->sentence(25),
            'fecha' => '2025-02-21',
            'hora_inicio' => '18:00',
            'duracion' => 55,
            'lugar' => function($attributes) {
                return $attributes['tipo'] === 'conferencia' ? 'Salón de Actos' : 'Aula de Talleres';
            },
            'cupo_maximo' => fake()->numberBetween(20, 50),
        ];
    }
}

