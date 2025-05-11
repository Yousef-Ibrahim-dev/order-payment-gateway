<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Interfaces\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Str;

class PaymentService
{
    protected array $gateways = [
        'credit_card' => \App\Services\Gateways\CreditCardGateway::class,
        'paypal'      => \App\Services\Gateways\PayPalGateway::class,
    ];

    /**
     * Process payment for the order via selected gateway.
     *
     * @param Order $order
     * @param array $data ['gateway','amount','metadata']
     * @return Payment
     * @throws Exception
     */
    public function create(Order $order, array $data): Payment
    {
        $key = $data['gateway'] ?? null;
        if (!isset($this->gateways[$key])) {
            throw new Exception("Unsupported gateway: {$key}");
        }

        $class = $this->gateways[$key];
        /** @var PaymentGatewayInterface $gateway */
        $gateway = new $class(config('gateways.'.$key, []));

        $payload = [
            'order_number' => $order->order_number,
            'amount'       => $data['amount'],
            'metadata'     => $data['metadata'] ?? [],
        ];

        try {
            // Process the payment via the selected gateway
            $result = $gateway->charge($payload);

            // If the response contains an approval URL, return it for redirection
            if (isset($result['approve_url'])) {
                return Payment::create([
                    'order_id'       => $order->id,
                    'amount'         => $payload['amount'],
                    'gateway'        => $key,
                    'transaction_id' => null,  // No transaction ID yet since the user needs to approve the payment
                    'status'         => 'pending',  // Pending since the user hasn't approved the payment yet
                    'approve_url'     => $result['approve_url'],  // Store the approval URL
                    'metadata'       => json_encode(['approve_url' => $result['approve_url']]),  // Store approve_url for redirection
                ]);
            }

            // If no approval is needed, create the payment in the database
            return Payment::create([
                'order_id'       => $order->id,
                'amount'         => $payload['amount'],
                'gateway'        => $key,
                'transaction_id' => $result['transaction_id'],
                'status'         => $result['status'],
                'metadata'       => json_encode($result['raw_response']),  // Store raw response for reference
            ]);
        } catch (Exception $e) {
            throw new Exception('Payment failed: ' . $e->getMessage());  // Handle errors gracefully
        }
    }


    /**
     * Paginate all payments.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage)
    {
        return Payment::with('order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }



}
