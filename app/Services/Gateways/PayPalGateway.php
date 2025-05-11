<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalGateway implements PaymentGatewayInterface
{
    protected PayPalClient $client;

    // Constructor to set PayPal API credentials
    public function __construct(array $config = [])
    {
        $this->client = new PayPalClient();
        $this->client->setApiCredentials(config('paypal'));
        $this->client->getAccessToken();
    }

    // Method to charge the customer via PayPal
    public function charge(array $data): array
    {
        // Data for the PayPal order creation
        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $data['order_number'],
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($data['amount'], 2, '.', ''),
                ],
            ]],

         /*   'application_context' => [
                'return_url' => route('paypal.callback'),  // URL to redirect after payment approval
            ],*/
        ];

        // Send request to PayPal to create the order
        $response = $this->client->createOrder($orderData);

        // Check if the order creation was successful
        if (!isset($response['id']) || $response['status'] !== 'CREATED') {
            throw new \Exception('PayPal order creation failed.');
        }

        // Check if there is an approval link for the payment
        if (isset($response['links'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    // Return the approval URL to be redirected to PayPal
                    return [
                        'approve_url' => $link['href'],  // URL to be redirected to for payment approval
                        'message' => 'Please approve the payment via PayPal.'
                    ];
                }
            }
        }

        // If the order doesn't need approval, proceed with capturing the payment
        $capture = $this->client->capturePaymentOrder($response['id']);

        // Check if `transaction_id` is available in the response
        $txnId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? $response['id'];

        // Determine the payment status (paid or failed)
        $status = $capture['status'] === 'COMPLETED' ? 'paid' : 'failed';

        return [
            'transaction_id' => $txnId,
            'status' => $status,
            'raw_response' => $capture,  // Return the raw response for debugging or storing metadata
        ];
    }


}
