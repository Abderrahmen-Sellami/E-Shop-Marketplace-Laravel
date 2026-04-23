<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
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
        $cart = $this->getCart();
        $items = []; // On utilise une liste générique d'items (produits ou services)
        $total = 0;

        foreach ($cart as $id => $details) {
            // Cas 1 : C'est un Service (identifié par le préfixe 'ser_')
            if (str_starts_with($id, 'ser_')) {
                $subtotal = $details['price'] * $details['quantity'];
                $total += $subtotal;
                $items[] = [
                    'id'       => $id,
                    'name'     => $details['name'],
                    'price'    => $details['price'],
                    'quantity' => $details['quantity'],
                    'subtotal' => $subtotal,
                    'type'     => 'service',
                    'image'    => null // Vous pouvez mettre une icône par défaut ici
                ];
            } 
            // Cas 2 : C'est un Produit (ID numérique classique)
            else {
                $product = Product::find($id);
                if ($product) {
                    $subtotal = $product->price * $details['quantity'];
                    $total += $subtotal;
                    $items[] = [
                        'id'       => $id,
                        'product'  => $product, // On garde l'objet pour l'image et le lien
                        'name'     => $product->title,
                        'price'    => $product->price,
                        'quantity' => $details['quantity'],
                        'subtotal' => $subtotal,
                        'type'     => 'product',
                        'image'    => $product->image_url
                    ];
                }
            }
        }

        // Note : On change 'products' par 'items' pour la vue
        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'integer|min:1|max:99']);
        $quantity = $request->get('quantity', 1);

        $cart = $this->getCart();
        
        // Structure uniforme pour les produits
        if(!isset($cart[$product->id])) {
            $cart[$product->id] = [
                "name" => $product->title,
                "quantity" => 0,
                "price" => $product->price,
                "type" => "product"
            ];
        }
        
        $cart[$product->id]['quantity'] += $quantity;
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['count' => array_sum(array_column($cart, 'quantity'))]);
        }

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function addService(Service $service)
    {
        $cart = $this->getCart();
        $id = 'ser_' . $service->id;
        if(!isset($cart[$id])) {
            $cart[$id] = [
                "name" => $service->name,
                "quantity" => 1,
                "price" => $service->price,
                "type" => "service"
            ];
        } else {
            $cart[$id]['quantity'] += 1;
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Service ajouté au panier !');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Panier mis à jour.');
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Élément retiré du panier.');
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