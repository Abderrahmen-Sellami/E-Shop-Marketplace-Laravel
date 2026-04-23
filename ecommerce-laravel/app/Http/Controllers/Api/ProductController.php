<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

   
    public function index(Request $request)
    {
        $query = Product::active()->with(['category:id,name', 'user:id,name']);

        if ($request->filled('search'))   $query->search($request->search);
        if ($request->filled('category')) $query->where('category_id', $request->category);

        $products = $query->paginate(12);

        return response()->json([
            'data'  => $products->items(),
            'meta'  => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ]
        ]);
    }

    
    public function show(Product $product)
    {
        $product->load(['category', 'user:id,name', 'reviews.user:id,name']);
        return response()->json($product);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['user_id'] = Auth::id();
        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

   
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return response()->json(['message' => 'Produit supprimé.']);
    }
}
