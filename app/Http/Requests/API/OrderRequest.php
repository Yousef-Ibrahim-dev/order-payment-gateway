<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class OrderRequest extends BaseRequest
{
    /**
     * Validation rules for creating/updating an order.
     * Ensures ordered quantity does not exceed product stock.
     */
    public function rules(): array
    {
        return [
            'items'                    => ['required','array','min:1'],
            'items.*.product_id'       => ['required','integer','exists:products,id'],
            'items.*.quantity'         => [
                'required','integer','min:1',
                function ($attribute, $value, $fail) {

                    $parts = explode('.', $attribute);
                    $index = $parts[1] ?? null;

                    if (null !== $index) {
                        $productId = $this->input("items.{$index}.product_id");
                        $product = Product::find($productId);
                        if ($product && $value > $product->stock) {
                            $fail("The quantity for product #{$product->id} ({$value}) exceeds available stock ({$product->stock}).");
                        }
                    }
                }
            ],
            'items.*.unit_price'       => ['required','numeric','min:0'],
            'items.*.product_name'     => ['sometimes','string','max:255'],
            'items.*.options'          => ['sometimes','array'],

            'shipping_address'         => ['required','array'],
            'shipping_address.street'  => ['required','string','max:255'],
            'shipping_address.city'    => ['required','string','max:100'],
            'shipping_address.postal_code' => ['required','string','max:20'],
            'shipping_address.country' => ['required','string','max:100'],

            'billing_address'          => ['sometimes','array'],
            'billing_address.street'   => ['required_with:billing_address','string','max:255'],
            'billing_address.city'     => ['required_with:billing_address','string','max:100'],
            'billing_address.postal_code' => ['required_with:billing_address','string','max:20'],
            'billing_address.country'  => ['required_with:billing_address','string','max:100'],

            'tax'                      => ['nullable','numeric','min:0'],
            'discount'                 => ['nullable','numeric','min:0'],
            'metadata'                 => ['nullable','array'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'items.required'                   => 'You must provide at least one order item.',
            'items.*.product_id.exists'        => 'One or more selected products do not exist.',
            'shipping_address.*.required'      => 'Shipping address :attribute is required.',
            'billing_address.*.required_with'  => 'Billing address :attribute is required when billing address is provided.',
        ];
    }
}
