<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Repositories\OrderRepository;
use App\Traits\ResponseTrait;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait, PaginationTrait;

    protected OrderService $service;
    protected OrderRepository $repo;

    public function __construct(OrderService $service, OrderRepository $repo)
    {
        $this->service = $service;
        $this->repo    = $repo;

    }

    public function index(Request $request)
    {
        $paginated = $this->repo->paginateByUser(
            auth()->id(),
            $request->input('per_page', 10),
            $request->input('status')
        );

        $data = OrderResource::collection($paginated)->resolve();
        $meta = $this->paginationModel($paginated);

        return $this->response('success', 'Orders fetched successfully', $data, ['meta' => $meta], true);
    }

    public function store(OrderRequest $request)
    {
        try {
            $order = $this->service->create($request->validated());
            $data  = (new OrderResource($order))->resolve();
            return $this->response('success', 'Order created successfully', $data);
        } catch (\Throwable $e) {
            return $this->response('exception', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function update(OrderRequest $request, Order $order)
    {
        try {
            $order = $this->service->update($order, $request->validated());
            $data  = (new OrderResource($order))->resolve();
            return $this->response('success', 'Order updated successfully', $data);
        } catch (\Throwable $e) {
            return $this->response('exception', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function show(int $order)
    {
        try {
            $order = $this->repo->find($order);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->response('fail', 'Order not found', [], [], false)
                ->setStatusCode(404);
        }

        $data = (new OrderResource($order))->resolve();

        return $this->response('success', 'Order details fetched successfully', $data);
    }

    public function destroy(int $order)
    {
        try {
            $this->service->delete($order);
            return $this->response('success', 'Order deleted successfully');
        } catch (\Exception $e) {
            return $this->response('fail', $e->getMessage());
        }
    }
}
