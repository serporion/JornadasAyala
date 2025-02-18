<?php

namespace App\Http\Controllers;


use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Evento;
use Illuminate\Http\Request;

class AlumnoController
{


    public function storeInscripcion(Request $request)
    {
        $user = $request->user(); // Usuario autenticado

        $validated = $request->validate([
            'tipo_inscripcion' => 'required|in:virtual,presencial,gratuita',
            'eventos' => 'required|array',
            'eventos.*' => 'exists:eventos,id',
        ]);

        // Validar que es un alumno si la inscripción es gratuita
        if ($validated['tipo_inscripcion'] === 'gratuita') {
            $alumno = Alumno::where('email', $user->email)->first();
            if (!$alumno) {
                return redirect()->back()->with('error', 'No puedes inscribirte como alumno porque no estás registrado como tal.');
            }
        }

        // Registrar inscripción
        $inscripcion = Inscripcion::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'tipo_inscripcion' => $validated['tipo_inscripcion'],
            'confirmado' => false,
            'pago_realizado' => $validated['tipo_inscripcion'] === 'gratuita' ? true : false,
        ]);

        // Asociar eventos
        foreach ($validated['eventos'] as $eventoId) {
            $inscripcion->eventos()->attach($eventoId);
        }

        return redirect()->route('pago.form', ['inscripcion_id' => $inscripcion->id]);
    }



}
