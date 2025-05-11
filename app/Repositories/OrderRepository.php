<?php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class OrderRepository
{
    public function paginateByUser(int $userId, int $perPage, ?string $status = null)
    {
        $query = Order::with(['items','payments'])
            ->where('user_id', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at','desc')
            ->paginate($perPage);
    }

    public function find(int $id): Order
    {

        $userId = auth()->id();
        $order = Order::with(['items','payments'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $order) {
            throw new ModelNotFoundException('Order not found for this user.');
        }

        return $order;
    }


}
