<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscripcionRequest;
use App\Models\Inscripcion;
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
        $user = auth()->user(); // Usuario autenticado

        // Filtrar eventos por tipo
        $conferencias = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'conferencia')
            ->get();

        $talleres = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'taller')
            ->get();

        // Contar el total de conferencias y talleres seleccionados
        $totalConferencias = $conferencias->count();
        $totalTalleres = $talleres->count();

        // Validar límites de selección
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

        // VALIDACIÓN ESPECÍFICA PARA INSCRIPCIÓN GRATUITA
        if ($request->input('tipo_inscripcion') === 'gratuita') {
            // Comprobamos si el correo del usuario está en la tabla `alumnos`
            $existeEnAlumnos = DB::table('alumnos')->where('email', $user->email)->exists();

            if (!$existeEnAlumnos) {
                // Si no está, volvemos al formulario con un error
                return redirect()->route('inscripcion.index')
                    ->withErrors(['error' => 'Su correo electrónico no está registrado como alumno.'])
                    ->withInput();
            }
        }

        // FILTRAR EVENTOS PARA RESUMEN
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();
        $totalCoste = $eventos->sum('costo'); // Suma de los costos de los eventos

        // Redirigir al resumen
        return view('inscripcion.resumen', [
            'eventos' => $eventos,
            'totalCoste' => $totalCoste,
            'tipo_inscripcion' => $request->input('tipo_inscripcion')
        ]);
    }


    public function confirmacion(Request $request)
    {
        // Decodificar los datos enviados desde el formulario de resumen
        $eventosSeleccionados = json_decode($request->input('eventos'), true);
        $tipoInscripcion = $request->input('tipo_inscripcion');
        $user = auth()->user(); // Usuario autenticado

        // Validar que los eventos aún existan y estén disponibles
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();

        if ($eventos->isEmpty() || $eventos->count() !== count($eventosSeleccionados)) {
            return redirect()->route('inscripcion.index')
                ->withErrors(['eventos' => 'Algunos de los eventos seleccionados ya no están disponibles. Intente nuevamente.']);
        }

        // Validar límites de selección de conferencias y talleres
        $conferencias = $eventos->where('tipo', 'conferencia');
        $talleres = $eventos->where('tipo', 'taller');

        if ($conferencias->count() > 5) {
            return redirect()->route('inscripcion.index')
                ->withErrors(['eventos' => 'No puedes inscribirte a más de 5 conferencias.'])
                ->withInput();
        }

        if ($talleres->count() > 4) {
            return redirect()->route('inscripcion.index')
                ->withErrors(['eventos' => 'No puedes inscribirte a más de 4 talleres.'])
                ->withInput();
        }

        // Validación de gratuidad repetida por seguridad
        if ($tipoInscripcion === 'gratuita') {
            $existeEnAlumnos = DB::table('alumnos')->where('email', $user->email)->exists();

            if (!$existeEnAlumnos) {
                return redirect()->route('inscripcion.index')
                    ->withErrors(['error' => 'Su correo electrónico no está registrado como alumno.'])
                    ->withInput();
            }
        }

        // Iniciar transacción para guardar inscripciones y proceder con el pago
        DB::beginTransaction();
        try {
            foreach ($eventosSeleccionados as $eventoId) {
                Inscripcion::create([
                    'user_id' => $user->id,
                    'evento_id' => $eventoId,
                    'tipo_inscripcion' => $tipoInscripcion,
                ]);
            }

            // Ejemplo: Proceso de pago con PayPal o cualquier otro servicio
            // $pagoExitoso = $this->procesarPago($user, $eventos->sum('costo'));
            // if (!$pagoExitoso) throw new \Exception('Error procesando el pago');

            DB::commit();

            return redirect()->route('inscripcion.exitosa')
                ->with('success', 'Inscripciones y pago completados exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('inscripcion.index')
                ->withErrors(['error' => 'Hubo un problema al procesar tu inscripción. Inténtalo nuevamente.']);
        }
    }
    /*
    //public function store(Request $request)
    public function store(InscripcionRequest $request)
    {

        // Validamos campos del formulario, pero lo hace el FormRequest propio.
        /*
        $request->validate([
            'eventos' => 'required|array|min:1', // Mínimo 1 evento seleccionado
            'tipo_inscripcion' => 'required|string', // Tipo de inscripción es obligatorio
        ]);


        $eventosSeleccionados = $request->input('eventos'); // IDs de los eventos seleccionados
        $user = auth()->user(); // Usuario autenticado

        // Filtrar eventos por tipo
        $conferencias = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'conferencia')
            ->get();

        $talleres = Evento::whereIn('id', $eventosSeleccionados)
            ->where('tipo', 'taller')
            ->get();

        // Contar el total de conferencias y talleres seleccionados
        $totalConferencias = $conferencias->count();
        $totalTalleres = $talleres->count();

        // Validar límites de selección
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

        // VALIDACIÓN ESPECÍFICA PARA INSCRIPCIÓN GRATUITA
        if ($request->input('tipo_inscripcion') === 'gratuita') {
            // Comprobamos si el correo del usuario está en la tabla `alumnos`
            $existeEnAlumnos = DB::table('alumnos')->where('email', $user->email)->exists();

            if (!$existeEnAlumnos) {
                // Si no está, volvemos al formulario con un error
                return redirect()->route('inscripcion.index')
                    ->withErrors(['error' => 'Su correo electrónico no está registrado como alumno.'])
                    ->withInput();
            }
        }



        // PROCESO DE INSCRIPCIÓN
        foreach ($eventosSeleccionados as $eventoId) {
            Inscripcion::create([
                'user_id' => auth()->id(),
                'evento_id' => $eventoId,
                'tipo_inscripcion' => $request->input('tipo_inscripcion'),
            ]);
        }

        // FILTRAR EVENTOS PARA RESUMEN
        $eventos = Evento::whereIn('id', $eventosSeleccionados)->get();
        $totalCoste = $eventos->sum('costo'); // Suma de los costos de los eventos

        // Redirigir a la vista de resumen
        return view('inscripcion.resumen', [
            'eventos' => $eventos,
            'totalCoste' => $totalCoste
        ]);
    }
    */

    /*
    public function store(Request $request)
    {
        //$data = $request->all(); // Obtener todos los datos del formulario como el del index pero tras la respuesta.

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
    */



}
