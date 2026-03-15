<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Product::forUser($user)->with(['category', 'design']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($request->input('per_page', 10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'design_id' => 'nullable|exists:designs,id',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|string',
            'image_path' => 'nullable|string'
        ]);

        $data = $validated;
        if ($request->user()->role === 'client') {
            $data['client_id'] = $request->user()->client?->id;
        }

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product->load(['category', 'design']);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'design_id' => 'nullable|exists:designs,id',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|string',
            'image_path' => 'nullable|string'
        ]);

        $product->update($validated);
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
