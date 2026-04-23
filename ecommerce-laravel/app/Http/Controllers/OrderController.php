<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product');
        return view('orders.show', compact('order'));
    }

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

        DB::transaction(function () use ($cart, $request) {
            $total = 0;
            $items = [];

            foreach ($cart as $productId => $item) {
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour {$product->title}");
                }

                $subtotal = $product->price * $item['quantity'];
                $total   += $subtotal;
                $items[]  = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id'          => Auth::id(),
                'status'           => 'pending',
                'total'            => $total,
                'shipping_address' => $request->shipping_address,
                'payment_method'   => $request->payment_method,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            session()->forget('cart');

            // Notification au vendeur
            foreach ($order->items as $item) {
                $item->product->user->notify(new \App\Notifications\NewOrderNotification($order));
            }
        });

        return redirect()->route('orders.index')
            ->with('success', 'Commande passée avec succès !');
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if (!$order->isPending()) {
            return back()->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        DB::transaction(function () use ($order) {
            // Remettre le stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $order->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Commande annulée.');
    }
}
