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
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret');
        $mode = config('services.paypal.mode'); // sandbox o live

        $environment = $mode === 'sandbox'
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function crearPago($monto, $moneda, $descripcion, $pedidoId)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $pedidoId,
                    'amount' => [
                        'currency_code' => $moneda,
                        'value' => $monto,
                    ],
                    'description' => $descripcion,
                ]
            ],
            'application_context' => [
                'return_url' => url("paypal/success?pedidoId={$pedidoId}"),
                'cancel_url' => url("paypal/cancel?pedidoId={$pedidoId}"),
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
