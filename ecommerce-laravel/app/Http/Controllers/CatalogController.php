<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'user', 'reviews']);

        // Recherche par mot-clé
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtrage par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtrage par prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Tri
        match($request->get('sort', 'latest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest'     => $query->oldest(),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        // AJAX — retourne uniquement les cartes produits
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('catalog.partials.products-grid', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return view('catalog.index', compact('products', 'categories'));
    }

    // Recherche AJAX live (barre de recherche)
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $products = Product::active()
            ->search($request->q)
            ->with('category')
            ->take(8)
            ->get(['id', 'title', 'price', 'image', 'slug']);

        return response()->json($products->map(fn($p) => [
            'id'       => $p->id,
            'title'    => $p->title,
            'price'    => number_format($p->price, 2),
            'image'    => $p->image_url,
            'url'      => route('products.show', $p),
            'category' => $p->category->name ?? '',
        ]));
    }
}
