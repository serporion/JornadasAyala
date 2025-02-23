<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PayPalService
{
    private $client;

    public function __construct()
    {
        $clientId =  config('app.PAYPAL_CLIENT_ID');
        $clientSecret = config('app.PAYPAL_SECRET');
        $mode = config('app.PAYPAL_MODE');

        $environment = $mode === 'sandbox'
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Método que crea un nuevo pago utilizando la API de PayPal.
     * Este método configura un pago en PayPal con los valores especificados,
     * incluidos el monto, la moneda y los detalles personalizados.
     * Se genera un enlace al que el usuario debe ser redirigido para aprobar el pago.
     *
     * @param float $monto Monto del pago.
     * @param string $moneda Código ISO de la moneda del pago (por ejemplo, USD).
     * @param string $detalleJson Detalles personalizados del pedido en formato JSON.
     *
     * @return string URL para redireccionar al usuario a la página de aprobación de PayPal.
     * @throws \Exception Si no se encuentra el enlace de aprobación o si ocurre un error al crear el pago.
     */
    public function crearPago($monto, $moneda, $detalleJson)
    {

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        /*
        $allConfigs = config()->all();
        dd($allConfigs);
        */

        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => uniqid('pedido_'),
                    'amount' => [
                        'currency_code' => $moneda,
                        'value' => $monto,
                    ],
                    'custom_id' => $detalleJson,
                ]
            ],
            'application_context' => [

                //Ensalada de rutas.
                // 'return_url' => url("paypal/success?pedidoId={$pedidoId}"),
                // 'cancel_url' => url("paypal/cancel?pedidoId={$pedidoId}"),

                //'return_url' => route('paypal/pagoExitoso'), // Redirigir aquí luego del éxito
                //'cancel_url' => route('paypal/pagoCancelado'), // Redirigir aquí si se cancela el pago

                //'return_url' => getenv('BASE_URLPAYPAL') . "PayPal/pagoExitoso?pedidoId={$pedidoId}",
                //'cancel_url' => getenv('BASE_URLPAYPAL') . "PayPal/pagoCancelado?pedidoId={$pedidoId}",

                'return_url' => config('app.BASE_URLPAYPAL') . "PayPal/pagoExitoso", //?eventoId=" . $eventoId,
                'cancel_url' => config('app.BASE_URLPAYPAL') . "PayPal/pagoCancelado", //?eventoId=" . $eventoId,
            ]
        ];

        try {
            $response = $this->client->execute($request);

            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    return $link->href; // URL para redirigir al usuario para la aprobación de PayPal
                }
            }

            throw new \Exception('No se encontró el enlace de aprobación de PayPal.');

        } catch (\Exception $e) {
            throw new \Exception("Error creando el pago en PayPal: " . $e->getMessage());
        }
    }

    /**
     * Método que captura un pago previamente aprobado utilizando la API de PayPal.
     * Este método toma el identificador de la orden y realiza la captura del pago
     * correspondiente, devolviendo el resultado de la transacción.
     *
     * @param string $orderId Identificador único de la orden en PayPal.
     *
     * @return object Resultado de la operación capturada de PayPal.
     * @throws \Exception Si ocurre un error durante la captura del pago.
     */
    public function capturarPago($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');

        try {
            $response = $this->client->execute($request);

            return $response->result;

        } catch (\Exception $e) {
            throw new \Exception("Error capturando el pago en PayPal: " . $e->getMessage());
        }
    }
}
