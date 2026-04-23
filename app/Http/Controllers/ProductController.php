<?php

namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

  public function index()
{
    $products = auth()->user()->products()->latest()->paginate(10); 

   
   
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
    if (Auth::id() !== $product->user_id && !Auth::user()->isAdmin()) abort(403);
    $categories = Category::all();
    return view('products.edit', compact('product', 'categories'));
}

public function update(Request $request, Product $product)
{
    if (Auth::id() !== $product->user_id && !Auth::user()->isAdmin()) abort(403);

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
    return redirect()->route('products.show', $product)->with('success', 'Produit mis à jour.');
}

public function destroy(Product $product)
{
    if (Auth::id() !== $product->user_id && !Auth::user()->isAdmin()) abort(403);

    if ($product->image) Storage::disk('public')->delete($product->image);
    $product->delete();
    return redirect()->route('products.index')->with('success', 'Produit supprimé.');
}
}
