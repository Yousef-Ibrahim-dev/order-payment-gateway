<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Services\PaymentService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ResponseTrait , PaginationTrait;

    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function store(PaymentRequest $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return $this->response('fail', 'Order not found', [], [], false)->setStatusCode(404);
        }

        // Check if the user is authorized to access the order
        if ($order->user_id !== auth()->id()) {
            return $this->response('fail', 'Unauthorized access to this order.');
        }

        if ($order->status !== 'confirmed') {
            return $this->response('fail', 'Order is not confirmed.');
        }

        try {
            // Create the payment for the order
            $payment = $this->service->create($order, $request->validated());

            // If PayPal approval is required, return the approval URL to the user
            if (isset($payment['approve_url'])) {
                return $this->response(
                    'success',
                    $payment['message'],
                    ['approve_url' => $payment['approve_url']]  // Return the PayPal approval URL
                );
            }

            // If payment is successful, return the payment details
            return $this->response(
                'success',
                'Payment processed successfully',
                (new PaymentResource($payment))->resolve()  // Process the payment resource for the response
            );
        } catch (\Exception $e) {
            return $this->response('exception', 'Payment failed: ' . $e->getMessage());  // Handle payment errors
        }
    }






    public function index(Request $request)
    {
        $payments = $this->service->paginate($request->input('per_page',10));
        $data     = PaymentResource::collection($payments)->resolve();
        $meta     = $this->paginationModel($payments);

        return $this->response('success','Payments fetched',$data,['meta'=>$meta],true);
    }

    public function forOrder(Order $order)
    {
        $payments = $order->payments()->paginate(10);
        $data     = PaymentResource::collection($payments)->resolve();
        $meta     = $this->paginationModel($payments);

        return $this->response('success','Order payments fetched',$data,['meta'=>$meta],true);
    }
}
