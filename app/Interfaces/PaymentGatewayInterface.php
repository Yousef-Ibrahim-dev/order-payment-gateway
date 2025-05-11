<?php


namespace App\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Charge the given payload.
     *
     * @param  array  $data  ['order_number','amount','metadata']
     * @return array         ['transaction_id','status','raw_response']
     *
     * @throws \Exception
     */
    public function charge(array $data): array;
}
