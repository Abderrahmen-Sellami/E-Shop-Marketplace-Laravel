<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    public function dashboard()
    {
        $stats = [
            'users'    => User::count(),
            'sellers'  => User::where('role', 'seller')->count(),
            'products' => Product::count(),
            'orders'   => Order::count(),
            'revenue'  => Order::where('status', 'validated')->sum('total'),
            'pending'  => Order::where('status', 'pending')->count(),
        ];

        $recentOrders  = Order::with('user')->latest()->take(5)->get();
        $recentUsers   = User::latest()->take(5)->get();
        $topProducts   = Product::withCount('orderItems')
                            ->orderByDesc('order_items_count')
                            ->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentUsers', 'topProducts'));
    }

    // ── Gestion utilisateurs ───────────────────────────────────
    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('search')) $query->where('name', 'like', "%{$request->search}%")
                                              ->orWhere('email', 'like', "%{$request->search}%");
        $users = $query->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,seller,buyer']);
        $user->update(['role' => $request->role]);
        return back()->with('success', "Rôle de {$user->name} mis à jour.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Impossible de supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

    // ── Gestion commandes ──────────────────────────────────────
    public function orders(Request $request)
    {
        $query = Order::with('user');
        if ($request->filled('status')) $query->where('status', $request->status);
        $orders = $query->latest()->paginate(15);
        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,validated,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    // ── Gestion produits ───────────────────────────────────────
    public function products(Request $request)
    {
        $query = Product::with(['user', 'category']);
        if ($request->filled('status')) $query->where('status', $request->status);
        $products = $query->latest()->paginate(15);
        return view('admin.products', compact('products'));
    }

    public function toggleProductStatus(Product $product)
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);
        return back()->with('success', 'Statut du produit mis à jour.');
    }

    // ── Statistiques JSON (pour graphiques) ───────────────────
    public function stats()
    {
        $ordersPerMonth = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as revenue')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($ordersPerMonth);
    }
}
