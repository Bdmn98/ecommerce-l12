<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'min_price' => ['nullable', 'numeric'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'name' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'exists:Category,id'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $page = $validated['page'] ?? 1;

        // Cache key
        $key = 'products:' . md5(json_encode($validated) . ':p' . $page);

        $products = cache()->remember($key, 60, function () use ($validated, $perPage) {
            return Product::with('category')
                ->filterByPrice($validated['min_price'] ?? null, $validated['max_price'] ?? null)
                ->searchByName($validated['name'] ?? null)
                ->when($validated['category_id'] ?? null, fn($q, $cid) => $q->where('category_id', $cid))
                ->paginate($perPage);
        });

        return $this->jsonResponseWithPagination($products, $products->total());
    }

    public function show(Product $product)
    {
        return $this->successfulResponse($product);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        cache()->flush(); // invalidate all product caches
        return $this->successfulResponse($product, __('Product created'), 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        cache()->flush();
        return $this->successfulResponse($product, __('Product updated'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        cache()->flush();
        return $this->successfulResponse(null, __('Product deleted'));
    }
}

