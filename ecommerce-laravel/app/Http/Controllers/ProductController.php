<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('verified')->except(['show']);
    }

    public function index()
    {
        $products = Auth::user()->isAdmin()
            ? Product::with(['category', 'user'])->latest()->paginate(12)
            : Product::where('user_id', Auth::id())->with('category')->latest()->paginate(12);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['user_id'] = Auth::id();

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Produit créé avec succès.');
    }

    public function show(Product $product)
    {
        $product->load(['user', 'category', 'reviews.user']);
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        $alreadyReviewed = Auth::check()
            ? $product->reviews()->where('user_id', Auth::id())->exists()
            : false;

        $inWishlist = Auth::check()
            ? Auth::user()->wishlistProducts()->where('product_id', $product->id)->exists()
            : false;

        return view('products.show', compact('product', 'relatedProducts', 'alreadyReviewed', 'inWishlist'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produit supprimé.');
    }
}
