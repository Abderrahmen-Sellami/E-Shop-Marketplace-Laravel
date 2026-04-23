<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Retourne le panier depuis la session
    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    public function index()
    {
        $cart     = $this->getCart();
        $products = [];
        $total    = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal    = $product->price * $item['quantity'];
                $total      += $subtotal;
                $products[]  = ['product' => $product, 'quantity' => $item['quantity'], 'subtotal' => $subtotal];
            }
        }

        return view('cart.index', compact('products', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'integer|min:1|max:99']);
        $quantity = $request->get('quantity', 1);

        $cart = $this->getCart();
        $cart[$product->id]['quantity'] = ($cart[$product->id]['quantity'] ?? 0) + $quantity;
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['count' => array_sum(array_column($cart, 'quantity'))]);
        }

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Panier mis à jour.');
    }

    public function remove($productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        session()->put('cart', $cart);

        return back()->with('success', 'Produit retiré du panier.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Panier vidé.');
    }

    public function count()
    {
        $cart = $this->getCart();
        return response()->json(['count' => array_sum(array_column($cart, 'quantity'))]);
    }
}
