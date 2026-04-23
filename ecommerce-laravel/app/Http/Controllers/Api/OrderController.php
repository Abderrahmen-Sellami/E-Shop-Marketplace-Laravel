<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

   
    public function index()
    {
        $orders = Auth::user()->isAdmin()
            ? Order::with(['user:id,name', 'items.product:id,title,price'])->latest()->paginate(15)
            : Auth::user()->orders()->with('items.product:id,title,price')->latest()->paginate(15);

        return response()->json($orders);
    }

    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product', 'user:id,name,email');
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $request->validate(['status' => 'required|in:pending,validated,cancelled']);
        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}
