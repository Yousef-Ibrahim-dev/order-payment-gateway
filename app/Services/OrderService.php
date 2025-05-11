<?php
namespace App\Services;

use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\ResponseTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{

    use  ResponseTrait;
    protected OrderRepository $repo;

    public function __construct(OrderRepository $repo)
    {
        $this->repo = $repo;
    }


    public function create(array $payload): Order
    {

        return DB::transaction(function() use ($payload) {
            $subTotal = collect($payload['items'])
                ->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $tax      = $payload['tax'] ?? 0;
            $discount = $payload['discount'] ?? 0;
            $total    = $subTotal + $tax - $discount;

            $data = [
                'uuid'             => Str::uuid(),
                'order_number'     => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'user_id'          => auth()->id(),
                'status'           => 'pending',
                'sub_total'        => $subTotal,
                'tax'              => $tax,
                'discount'         => $discount,
                'currency'         => 'USD',
                'total'            => $total,
                'shipping_address' => $payload['shipping_address'],
                'billing_address'  => $payload['billing_address'] ?? $payload['shipping_address'],
                'metadata'         => $payload['metadata'] ?? null,
            ];

            $order = Order::create($data);

            foreach ($payload['items'] as $item) {
                $order->items()->create([
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'] ?? '',
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total_price'  => $item['quantity'] * $item['unit_price'],
                    'options'      => $item['options'] ?? [],
                ]);
            }

            return $order->load(['items','payments']);
        });
    }

    public function update(Order $order, array $payload): Order
    {
        if ($order->user_id !== auth()->id()) {
            throw new \Exception('You are not authorized to modify this order.');
        }
        // Prevent updates if order is cancelled or returned
        if (in_array($order->status, ['cancelled', 'returned'])) {
            throw new \Exception('Cannot update a cancelled or returned order.');
        }

        // Prevent updates if any payment is already paid
        if ($order->payments()->where('status', 'paid')->exists()) {
            throw new \Exception('Cannot update an order with paid payments.');
        }

        return DB::transaction(function() use ($order, $payload) {
            // Recalculate and update if items provided
            if (isset($payload['items'])) {
                $order->items()->delete();

                $subTotal = collect($payload['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
                $tax      = $payload['tax'] ?? $order->tax;
                $discount = $payload['discount'] ?? $order->discount;
                $total    = $subTotal + $tax - $discount;

                $order->update([
                    'sub_total' => $subTotal,
                    'tax'       => $tax,
                    'discount'  => $discount,
                    'total'     => $total,
                ]);

                foreach ($payload['items'] as $item) {
                    $order->items()->create([
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['product_name'] ?? '',
                        'quantity'     => $item['quantity'],
                        'unit_price'   => $item['unit_price'],
                        'total_price'  => $item['quantity'] * $item['unit_price'],
                        'options'      => $item['options'] ?? [],
                    ]);
                }
            }

            // Update other fields if provided
            $order->update(array_filter([
                'shipping_address' => $payload['shipping_address'] ?? null,
                'billing_address'  => $payload['billing_address']  ?? null,
                'metadata'         => $payload['metadata']         ?? null,
                'status'           => $payload['status']           ?? null,
            ], fn($v) => !is_null($v)));

            return $order->load(['items','payments']);
        });
    }

    public function delete(int $id): bool
    {
        $order = $this->repo->find($id);

        if ($order->user_id !== auth()->id()) {
            throw new \Exception('You are not authorized to delete this order.');
        }

        // Prevent deletion if order is cancelled or returned
        if (in_array($order->status, ['cancelled', 'returned'])) {
            throw new \Exception('Cannot delete a cancelled or returned order.');
        }

        // Prevent deletion if any payment is already paid
        if ($order->payments()->where('status', 'paid')->exists()) {
            throw new \Exception('Cannot delete an order with paid payments.');
        }

        return $order->delete();
    }


}
