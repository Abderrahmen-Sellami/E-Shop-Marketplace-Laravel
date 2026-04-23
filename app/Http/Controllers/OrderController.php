<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Liste des commandes de l'utilisateur connecté
     */
    public function index()
    {
        // On charge les relations pour éviter le problème N+1
        $orders = Auth::user()->orders()
            ->with(['items.product', 'items.service'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Détails d'une commande spécifique
     */
 public function show(Order $order)
{
    $user = Auth::user();

    // 1. L'ADMIN voit TOUT (Priorité maximale)
    if ($user->role === 'admin' || $user->isAdmin()) {
        $order->load(['items.product', 'items.service', 'user']);
        return view('orders.show', compact('order'));
    }

    // 2. L'ACHETEUR (Le client ID 4) voit sa propre commande
    if ($user->id == $order->user_id) {
        $order->load(['items.product', 'items.service', 'user']);
        return view('orders.show', compact('order'));
    }

    // 3. LE VENDEUR (Le propriétaire ID 2) voit la commande s'il doit livrer un article
    // On vérifie si l'utilisateur est le propriétaire d'un produit ou service dans cette commande
    $isOwnerOfAnItem = $order->items()->where(function($query) use ($user) {
        $query->whereHas('product', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orWhereHas('service', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    })->exists();

    if ($isOwnerOfAnItem) {
        $order->load(['items.product', 'items.service', 'user']);
        return view('orders.show', compact('order'));
    }

    // 4. SI AUCUN DES TROIS : On bloque
    abort(403, "Désolé, vous n'avez pas accès à cette commande.");
}

    /**
     * Validation du panier et création de la commande
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'payment_method'   => 'required|in:cash,card,paypal',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $itemsToCreate = [];

            foreach ($cart as $id => $item) {
                // --- CAS 1 : SERVICE ---
                if (str_starts_with($id, 'ser_')) {
                    $serviceId = str_replace('ser_', '', $id);
                    $service = Service::find($serviceId);

                    if (!$service) throw new \Exception("Le service {$item['name']} n'est plus disponible.");

                    $subtotal = $service->price * $item['quantity'];
                    $total += $subtotal;
                    
                    $itemsToCreate[] = [
                        'service_id'    => $service->id,
                        'product_id'    => null,
                        'quantity'      => $item['quantity'],
                        'unit_price'    => $service->price,
                        'seller'        => $service->user // Pour la notification
                    ];
                } 
                // --- CAS 2 : PRODUIT ---
                else {
                    $product = Product::lockForUpdate()->find($id);

                    if (!$product) throw new \Exception("Le produit {$item['name']} n'existe plus.");

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->title} (Disponible: {$product->stock})");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;
                    
                    $itemsToCreate[] = [
                        'product_id'    => $product->id,
                        'service_id'    => null,
                        'quantity'      => $item['quantity'],
                        'unit_price'    => $product->price,
                        'seller'        => $product->user // Pour la notification
                    ];

                    // Réduction du stock
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Création de la commande
            $order = Order::create([
                'user_id'          => Auth::id(),
                'status'           => 'pending',
                'total'            => $total,
                'shipping_address' => $request->shipping_address,
                'payment_method'   => $request->payment_method,
            ]);

            // Création des lignes de commande et notifications
            foreach ($itemsToCreate as $data) {
                $order->items()->create([
                    'product_id' => $data['product_id'],
                    'service_id' => $data['service_id'],
                    'quantity'   => $data['quantity'],
                    'unit_price' => $data['unit_price'],
                ]);

                // Notification au vendeur (si présent)
                if ($data['seller']) {
                    $data['seller']->notify(new \App\Notifications\NewOrderNotification($order));
                }
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('orders.index')->with('success', 'Commande validée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Annulation de la commande
     */
    public function cancel(Order $order)
    {
        if (Auth::id() !== $order->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    // On ne rend le stock QUE si c'est un produit
                    if ($item->product_id) {
                        $item->product()->increment('stock', $item->quantity);
                    }
                }
                $order->update(['status' => 'cancelled']);
            });

            return back()->with('success', 'Commande annulée et stock mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de l'annulation.");
        }
    }
}