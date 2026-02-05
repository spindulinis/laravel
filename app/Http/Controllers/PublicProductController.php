<?php

namespace App\Http\Controllers;

use App\Dtos\PageSerializeDto;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int)$request->query('limit', 10);
        $offset = (int)$request->query('offset', 0);

        $products = Product::offset($offset)
            ->limit($limit)
            ->get();

        return response()->json(new PageSerializeDto(
            items: ProductResource::collection($products)->resolve(),
            total: Product::count(),
            limit: $limit,
            offset: $offset
        ));
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}
