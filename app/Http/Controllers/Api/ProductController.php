<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    use ResponseTrait , PaginationTrait;

    public function index(Request $request)
    {
        $perPage  = $request->input('per_page', 10);
        $products = Product::orderBy('created_at','desc')
            ->paginate($perPage);

        $data = ProductResource::collection($products)->resolve();
        $meta = $this->paginationModel($products);

        return $this->response(
            'success',
            'Products fetched successfully',
            $data,
            ['meta' => $meta],
            true
        );
    }


    public function show(Product $product)
    {
        $data = (new ProductResource($product))->resolve();

        return $this->response(
            'success',
            'Product details fetched successfully',
            $data
        );
    }
}
