<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PayPalController extends Controller
{
    private $paypalService;

    public function __construct()
    {
        $this->paypalService = new PayPalService();
        $this->inscripcion = new InscripcionController();
    }

    public function iniciarPago(int $importeTotal, $detalleJson)
    {
        try {

            $approvalLink = $this->paypalService->crearPago(
                $importeTotal,
                'EUR',
                $detalleJson,
            );

            //return redirect($approvalLink); // No funciona!!!!!!
            // Redirige a PayPal para la aprobación del pago

            header("Location: " . $approvalLink);
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Error al iniciar el pago: " . $e->getMessage());
        }
    }


    public function pagoExitoso(Request $request)
    {
        $eventoId = $request->query('eventoId');
        $token = $request->query('token');
        $payerId = $request->query('PayerID');


        if (!$token || !$payerId) {
            return redirect()->route('home')->with('error', 'Datos de pago incompletos.');
        }

        DB::beginTransaction();

        try {
            $captura = $this->paypalService->capturarPago($token);

            $total = $captura->purchase_units[0]->amount->value;

            $detalleJson = null;
            if (isset($captura->purchase_units[0]->custom_id)) {
                $detalleJson = $captura->purchase_units[0]->custom_id; // Aquí recuperamos el "detalleJson" enviado a PayPal
            }

            if ($captura->status === 'COMPLETED') {

                $user = auth()->user();

                $transactionId = null;
                foreach ($captura->purchase_units as $unit) {
                    if (!empty($unit->payments->captures)) {
                        foreach ($unit->payments->captures as $capture) {
                            if ($capture->status === 'COMPLETED') {
                                $transactionId = $capture->id;
                                break;
                            }
                        }
                    }
                }

                if (!$transactionId) {
                    throw new \Exception("No se encontró una transacción válida.");
                }


                DB::table('pago_paypals')->insert([
                    'user_id' => $user->id,
                    'transaccionPaypal' => $captura->id,
                    'total' => $total,
                    'eventos' => json_encode([$detalleJson]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                $resultado = $this->inscripcion->gestionarTransaccion($detalleJson, $total);

                DB::commit();

                return redirect()->route('dashboard')->with('success', 'Pago e inscripción finalizados correctamente. Revisa tu bandeja de entrada para más detalles.');
            }

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('dashboard')->with('error', 'Error procesando el pago: ' . $e->getMessage());
        }
    }
    public function pagoCancelado(Request $request)
    {
        return redirect()->route('home')->with('error', 'El pago fue cancelado.');
    }
}
