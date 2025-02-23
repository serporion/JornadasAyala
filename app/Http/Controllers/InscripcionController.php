<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscripcionRequest;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\TipoInscripcion;
use App\Services\Mail;
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
        $eventosSeleccionados = $request->input('eventos');
        $detalleInscripcion = [];
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

            $costoEvento = ($tipoInscripcion === 'gratuita') ? 0 : (($tipoInscripcion === 'virtual') ? 10 : 20);

            $detalleInscripcion[] = [
                'evento' => $evento->nombre,
                'tipo_inscripcion' => ucfirst($tipoInscripcion),
                'costo' => $costoEvento,
            ];

            $evento->costo = $costoEvento;
        }

        $totalCoste = collect($detalleInscripcion)->sum('costo');

        // Redirigimos al resumen incluyendo los eventos seleccionados
        return view('inscripcion.resumen', [
            'detalle' => $detalleInscripcion,
            'totalCoste' => $totalCoste,
            'eventos' => $eventos, // Incluimos todos los eventos seleccionados
        ]);
    }

    public function iniciarProcesoPago(Request $request)
    {
        $user = auth()->user();

        try {

            $eventosSeleccionados = json_decode($request->input('eventos'), true);

            $event = json_decode($request->input('detalle'), true);

            foreach ($event as $evento) {
                if($evento['tipo_inscripcion'] === 'Gratuita') {
                    $alumno = Alumno::where('email', $user->email)->first();
                    if (!$alumno) {
                        return redirect()->route('dashboard')->with('error', 'No puedes inscribirte como alumno porque no estás registrado como tal.');
                    }
                }
            }

            // Valido plazas
            $verificacionExitosa = $this->verificarPlazas($eventosSeleccionados);

            if (!$verificacionExitosa) {
                throw new \Exception('Error verificando las plazas existentes.');
            }

            $importeTotal = $request->input("totalCoste");

            $detalleJson = $request->input('detalle');

            if (!is_string($detalleJson) || json_decode($detalleJson, true) === null) {
                throw new \Exception('El campo detalle no contiene un JSON válido.');
            }

            $detalles = json_decode($detalleJson, true);


            // Usa referencia (&) para modificar el detalle en el array original
            foreach ($detalles as $index => &$detalle) {
                $eventoId = $eventosSeleccionados[$index] ?? null;
                if ($eventoId) {
                    $detalle['eventoId'] = $eventoId;
                }
            }

            $detalleCompletoJson = json_encode($detalles);

            $paypalController = resolve(PayPalController::class);
            return $paypalController->iniciarPago($importeTotal, $detalleCompletoJson);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error procesando el pago: ' . $e->getMessage());
        }
    }

    public function gestionarTransaccion($detalleJson, $total)
    {

        DB::beginTransaction();

        try {

            //Vuelvo a validar por si se devuelve algo incorrecto.
            if (!is_string($detalleJson) || json_decode($detalleJson, true) === null) {
                throw new \Exception('JSON no válido.');
            }

            //Realizamos las inscripciones
            $confirmacionExitosa = $this->confirmacion($detalleJson);
            if (!$confirmacionExitosa) {
                throw new \Exception('Error durante la grabación de las inscripciones.');
            }

            $detalles = json_decode($detalleJson, true);

            $eventosIds = [];

            foreach ($detalles as $index => $detalle) {
                if (isset($detalle['eventoId'])) {
                    $eventosIds[] = $detalle['eventoId']; // Almacenar los IDs de eventos
                }
            }

            $eventos = Evento::whereIn('id', $eventosIds)->get();

            foreach ($eventos as $evento) {
                $evento->restarPlaza('cupo_maximo');
            }

            DB::commit();

            $mailService = resolve(Mail::class);
            $correoEnviado = $mailService->sendInscriptionDetails($detalleJson, $total);

            if (!$correoEnviado) {
                throw new \Exception('No se pudo enviar el correo con los detalles.');
            }


        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('inscripcion.index')
                ->withErrors(['error' => 'Error procesando el pago: ' . $e->getMessage()]);
        }
    }


    private function confirmacion($detalleJson)
    {

        $user = auth()->user();

        $detalle = json_decode($detalleJson, true);


        foreach ($detalle as $key => $evento) {
            if (is_array($evento) && isset($evento['eventoId'])) {

                $tipoInscripcionId = TipoInscripcion::where('nombre', $evento['tipo_inscripcion'])->value('id');

                //Crear el registro en inscripcion
                $inscripcion = Inscripcion::create([
                    'user_id' => $user->id,
                    'tipo_inscripcion_id' => $tipoInscripcionId,
                    'fecha_inscripcion' => now()
                ]);

                $inscripcionId = $inscripcion->id;

                // Crear el registro en inscripcion_eventos
                DB::table('inscripcion_eventos')->insert([
                    'inscripcion_id' => $inscripcionId,
                    'evento_id' => $evento['eventoId'],
                ]);

            }
        }

        return true;
    }

    private function verificarPlazas($eventosSeleccionados)
    {
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

        if ($eventos->isEmpty() || $eventos->count() !== count($eventosSeleccionados)) {
            return false; // Error: Hay eventos no válidos
        }

        foreach ($eventos as $evento) {
            if ($evento->cupo_maximo <= 0) {
                return false; // No hay plazas disponibles
            }

        }
        return true;
    }


}
