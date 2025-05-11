<?php
// app/Services/Gateways/CreditCardGateway.php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function __construct(array $config = [])
    {

    }

    public function charge(array $data): array
    {

        $transactionId = 'CC-' . strtoupper(uniqid());
        return [
            'transaction_id' => $transactionId,
            'status'         => 'paid',
            'raw_response'   => ['message' => 'Charged via Credit Card.'],
        ];
    }
}
