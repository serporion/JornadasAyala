<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    private $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function iniciarPago($pedidoId)
    {
        // Obtén los detalles del pedido desde tu modelo o servicio relevante
        $pedido = Pedido::find($pedidoId);

        if (!$pedido || $pedido->pagado) {
            return redirect()->back()->with('error', 'El pedido no existe o ya está pagado.');
        }

        try {
            $approvalLink = $this->paypalService->crearPago(
                $pedido->total, // Monto total del pedido
                'EUR',          // Moneda
                "Pago del pedido #{$pedidoId}", // Descripción
                $pedidoId       // ID del pedido
            );

            return redirect($approvalLink); // Redirige al enlace de PayPal

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Error al iniciar el pago: " . $e->getMessage());
        }
    }

    public function pagoExitoso(Request $request)
    {
        $pedidoId = $request->query('pedidoId');
        $token = $request->query('token');
        $payerId = $request->query('PayerID');

        if (!$token || !$payerId) {
            return redirect()->route('home')->with('error', 'Datos de pago incompletos.');
        }

        try {
            $captura = $this->paypalService->capturarPago($token);

            if ($captura->status === 'COMPLETED') {
                $pedido = Pedido::find($pedidoId);
                $pedido->update([
                    'pagado' => true,
                    'transaction_id' => $captura->id,
                ]);

                // Opcional: enviar correo electrónico
                // Mail::to($pedido->user->email)->send(new ConfirmacionPago($pedido));

                return redirect()->route('home')->with('success', 'Pago exitoso.');
            } else {
                throw new \Exception('El estado del pago no es COMPLETED.');
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', "Error procesando el pago: " . $e->getMessage());
        }
    }

    public function pagoCancelado(Request $request)
    {
        return redirect()->route('home')->with('error', 'El pago fue cancelado.');
    }
}
