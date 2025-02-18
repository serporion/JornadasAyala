<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;
use App\Models\Evento;

class InscripcionController extends Controller
{
    public function index()
    {
        /*
        $inscripcion = Inscripcion::all();
        return view('inscripcion.index', compact('inscripcion')); // Me hace falta afinar
        */

        $eventos = Evento::select('id', 'tipo', 'nombre', 'descripcion', 'fecha', 'hora_inicio', 'lugar')
            ->orderBy('hora_inicio', 'asc')
            ->get();


        return view('inscripcion.formulario', ['eventos' => $eventos]);


    }

    public function store(Request $request)
    {
        $data = $request->all(); // Obtener todos los datos del formulario como el del index pero tras la respuesta.

        //Valido si el campo está presente y si por lo menos hay 1
        $request->validate([
            'eventos' => 'required|array|min:1',
            'tipo_inscripcion' => 'required|string',
        ]);


        $eventosSeleccionados = $request->input('eventos');

        // Uso Eloquent para determinar que son
        $conferencias = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'conferencia')
            ->get();

        $talleres = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'taller')
            ->get();

        // Contar los conferencias y talleres
        $totalConferencias = $conferencias->count();
        $totalTalleres = $talleres->count();

        // Validar las restricciones
        if ($totalConferencias > 5) {
            return redirect()->back()->withErrors([
                'eventos' => 'Solo puedes seleccionar un máximo de 5 conferencias.',
            ])->withInput();
        }

        if ($totalTalleres > 4) {
            return redirect()->back()->withErrors([
                'eventos' => 'Solo puedes seleccionar un máximo de 4 talleres.',
            ])->withInput();
        }

        // Inscribir al usuario (proceso de inscripción)
        foreach ($eventosSeleccionados as $eventoId) {
            Inscripcion::create([
                'user_id' => auth()->id(),
                'evento_id' => $eventoId,
                'tipo_inscripcion' => $request->input('tipo_inscripcion'),
            ]);
        }

        return redirect()->route('inscripcion.exitosa')->with('success', 'Inscripción completada exitosamente.');
    }


}
