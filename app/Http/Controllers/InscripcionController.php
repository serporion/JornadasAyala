<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscripcionRequest;
use App\Models\Inscripcion;
use App\Models\TipoInscripcion;
use Illuminate\Http\Request;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;

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

    public function store(InscripcionRequest $request)
    {
        $eventosSeleccionados = $request->input('eventos'); // IDs de los eventos seleccionados
        $detalleInscripcion = []; // Para almacenar el detalle de inscripción
        $user = auth()->user(); // Usuario autenticado


        // Filtrar eventos seleccionados
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

        // Validar límites de selección (conferencias y eventos ) y tipoInscripcion,
        // definidas de forma personal, no en el RequestForm.
        $conferencias = $eventos->where('tipo', 'conferencia');
        $talleres = $eventos->where('tipo', 'taller');

        if ($conferencias->count() > 5) {
            return redirect()->back()->withErrors([
                'eventos' => 'Solo puedes seleccionar un máximo de 5 conferencias.',
            ])->withInput();
        }

        if ($talleres->count() > 4) {
            return redirect()->back()->withErrors([
                'eventos' => 'Solo puedes seleccionar un máximo de 4 talleres.',
            ])->withInput();
        }

        foreach ($eventos as $evento) {
            $tipoInscripcion = $request->input("tipo_inscripcion_{$evento->id}"); // Radio dinámico

            if (!$tipoInscripcion) {
                return redirect()->back()->withErrors([
                    "tipo_inscripcion_error_{$evento->id}" => 'Debe seleccionar el tipo de inscripción para este evento.',
                ])->withInput();
            }

            // Procesar el costo según el tipo inscripción
            $costoEvento = ($tipoInscripcion === 'gratuita') ? 0 : (($tipoInscripcion === 'virtual') ? 10 : 20);

            $detalleInscripcion[] = [
                'evento' => $evento->nombre,
                'tipo_inscripcion' => ucfirst($tipoInscripcion),
                'costo' => $costoEvento,
            ];

            // Agregar datos adicionales del evento al detalle (si es necesario)
            $evento->costo = $costoEvento; // Añadimos costo al modelo para usarlo en la vista
        }

        $totalCoste = collect($detalleInscripcion)->sum('costo'); // Sumar costos

        // Redirigir al resumen incluyendo los eventos seleccionados
        return view('inscripcion.resumen', [
            'detalle' => $detalleInscripcion,
            'totalCoste' => $totalCoste,
            'eventos' => $eventos, // Incluimos todos los eventos seleccionados
        ]);
    }

    public function gestionarTransaccion(Request $request)
    {
        $user = auth()->user(); // Usuario autenticado
        $eventosSeleccionados = json_decode($request->input('eventos'), true);
        $tipoInscripcionNombre = $request->input('tipo_inscripcion_0');


        // BUSCAR EL ID DEL TIPO DE INSCRIPCIÓN
        $tipoInscripcionId = TipoInscripcion::where('nombre', $tipoInscripcionNombre)->value('id');


        // Transacción de Control

        DB::beginTransaction();

        try {

            if (!$tipoInscripcionId) {
                throw new \Exception("No se encontró un tipo de inscripción con el nombre: $tipoInscripcionNombre");
            }

            // 1. Validar y restar plazas
            $verificacionExitosa = $this->verificarYRestarPlazas($user, $eventosSeleccionados);
            if (!$verificacionExitosa) {
                throw new \Exception('Error verificando o restando plazas.');
            }

            // 2. Realizar las inscripciones
            $confirmacionExitosa = $this->confirmacion($user, $eventosSeleccionados, $tipoInscripcionId);
            if (!$confirmacionExitosa) {
                throw new \Exception('Error durante las inscripciones.');
            }


            // 3. Procesar el pago

            $paypalController = resolve(PayPalController::class);
            $paypalController->iniciarPago($eventosSeleccionados);

            return null;

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('inscripcion.index')
                ->withErrors(['error' => 'Error procesando el pago: ' . $e->getMessage()]);
        }
    }


    private function confirmacion($user, $eventosSeleccionados, $tipoInscripcionId)
    {
        foreach ($eventosSeleccionados as $eventoId) {
            //Creamos la inscripción en las tablas correspondientes.
            //En esta variable grabamos la inscripcion realizada y luego recuperamos su id.

            $evento = Evento::findOrFail($eventoId);

            $inscripcion = Inscripcion::create([
                'user_id' => $user->id,
                'tipo_inscripcion_id' => $tipoInscripcionId,
                'fecha_inscripcion' => now()
            ]);

            // Crear el registro en inscripcion_eventos
            DB::table('inscripcion_eventos')->insert([
                'inscripcion_id' => $inscripcion->id,
                'evento_id' => $evento->id,
            ]);


        }

        return true;
    }


    private function verificarYRestarPlazas($user, $eventosSeleccionados)
    {
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

        if ($eventos->isEmpty() || $eventos->count() !== count($eventosSeleccionados)) {
            return false; // Error: Hay eventos no válidos
        }

        foreach ($eventos as $evento) {
            if ($evento->cupo_maximo <= 0) {
                return false; // Error: No hay plazas disponibles
            }
            // Restar plaza
            $evento->restarPlaza('cupo_maximo');
        }

        return true; // Todo válido
    }


}
