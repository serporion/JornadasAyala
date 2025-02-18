<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoInscripcion>
 */
class TipoInscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [  //Abuso de factoria.

            /*
            'nombre' => 'presencial',
            'precio' => 20.00,  // Precio más alto por incluir asistencia física y aperitivos




            'nombre' => 'virtual',
            'precio' => 10.00,  // Precio intermedio por ser online

            */

            'nombre' => 'gratuita',
            'precio' => 0.00,   // Gratuita para estudiantes del curso de especialización



        ];
    }
}
