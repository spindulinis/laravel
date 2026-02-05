<?php

namespace App\Http\Controllers;

use App\Dtos\PageSerializeDto;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductListItemResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int)$request->query('limit', 10);
        $offset = (int)$request->query('offset', 0);

        $products = Product::offset($offset)
            ->limit($limit)
            ->get();

        return response()->json(new PageSerializeDto(
            items: ProductListItemResource::collection($products)->resolve(),
            total: Product::count(),
            limit: $limit,
            offset: $offset
        ));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $product = Product::create($validated);
        $product->categories()->sync($request->category_ids);
        return new ProductResource($product->load('categories'));
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $product->update($validated);
        return new ProductResource($product->load('categories'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
