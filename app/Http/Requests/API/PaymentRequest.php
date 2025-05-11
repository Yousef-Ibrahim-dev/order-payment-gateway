<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends BaseRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function rules(): array
    {
        return [
            'gateway'  => 'required|string|in:credit_card,paypal',
            'amount'   => 'required|numeric|min:0.01',
            'metadata' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'gateway.required' => 'The payment gateway is required.',
            'gateway.string'   => 'The payment gateway must be a string.',
            'gateway.in'       => 'The selected payment gateway is invalid.',
            'amount.required'  => 'The amount is required.',
            'amount.numeric'   => 'The amount must be a number.',
            'amount.min'       => 'The amount must be at least :min.',
            'metadata.array'   => 'The metadata must be an array.',
        ];
    }
}
