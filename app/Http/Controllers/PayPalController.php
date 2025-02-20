<?php

namespace App\Http\Controllers;

use App\Services\Mail;
use App\Services\PayPalService;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Mail;
use App\Models\Evento; // Modelo de Evento
use App\Models\Inscripcion; // Modelo para las inscripciones
use App\Mail\EntradaInscripcion;
use Illuminate\Support\Facades\DB;

//use Illuminate\Support\Facades\Mail;

// Clase para enviar la entrada

class PayPalController extends Controller
{
    private $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function iniciarPago(array $eventosIds)
    {
        // Busca el evento y realiza comprobaciones
        $eventos = Evento::whereIn('id', $eventosIds)->get();

        if ($eventos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron eventos válidos.');
        }

        try {

            // Calcular el total a pagar sumando los precios de los eventos
            $totalPrecio = $eventos->sum('precio');

            // Crear una descripción para el pago (e.g., incluir nombres de eventos)
            $descripcion = "Pago para los eventos: " . $eventos->pluck('nombre')->join(', ');

            // Serializar los eventos seleccionados para usarlos más adelante (como `$customData`)
            $customData = json_encode([
                'eventos' => $eventosIds, // IDs de eventos seleccionados
                'user_id' => auth()->id(), // ID del usuario que realiza el pago
            ]);

            $approvalLink = $this->paypalService->crearPago(
                $totalPrecio,    // Monto total
                'EUR',           // Moneda
                $descripcion,    // Descripción del pago
                $customData      // Tu dato personalizado (serializado a JSON)
            );

            return redirect($approvalLink); // Redirige al enlace de PayPal

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Error al iniciar el pago: " . $e->getMessage());
        }
    }

    public function pagoExitoso(Request $request)
    {
        //$eventoId = $request->query('eventoId'); // Aquí estamos utilizando el evento como "pedido"
        $token = $request->query('token');
        $payerId = $request->query('PayerID');

        if (!$token || !$payerId) {
            return redirect()->route('home')->with('error', 'Datos de pago incompletos.');
        }

        //DB::beginTransaction();

        try {
            $captura = $this->paypalService->capturarPago($token);

            if ($captura->status === 'COMPLETED') {

                // Encuentra el evento para registrarlo como pagado
                /*
                $evento = Evento::find($eventoId);
                if (!$evento) {
                    return redirect()->route('home')->with('error', 'El evento no fue encontrado.');
                }
                */

                //$customData = json_decode($captura->purchase_units[0]->custom_id);


                $eventosSeleccionados = [/* IDs de eventos seleccionados aquí */];
                $user = auth()->user();

                // Enviar detalles de inscripciones a través del nuevo método del servicio Mail
                $mailService = resolve(Mail::class);
                $correoEnviado = $mailService->sendInscriptionDetails($user, $eventosSeleccionados);

                if (!$correoEnviado) {
                    throw new \Exception('No se pudo enviar el correo con los detalles.'); // Manejo de error si falla
                }

                DB::commit();

                return redirect()->route('home')->with('success', 'Pago e inscripción finalizados correctamente. Revisa tu bandeja de entrada para más detalles.');

            } else {
                throw new \Exception('El estado del pago no es COMPLETED.');
            }

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('home')->with('error', 'Error procesando el pago: ' . $e->getMessage());
        }
    }
    public function pagoCancelado(Request $request)
    {
        return redirect()->route('home')->with('error', 'El pago fue cancelado.');
    }
}
