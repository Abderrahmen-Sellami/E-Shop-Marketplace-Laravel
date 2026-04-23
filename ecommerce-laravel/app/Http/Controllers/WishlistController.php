<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $items = Auth::user()->wishlistProducts()->with('category')->paginate(12);
        return view('wishlist.index', compact('items'));
    }

    public function toggle(Product $product)
    {
        $user = Auth::user();
        $exists = $user->wishlistProducts()->where('product_id', $product->id)->exists();

        if ($exists) {
            $user->wishlistProducts()->detach($product->id);
            $inWishlist = false;
            $message = 'Retiré de la wishlist.';
        } else {
            $user->wishlistProducts()->attach($product->id);
            $inWishlist = true;
            $message = 'Ajouté à la wishlist.';
        }

        if (request()->ajax()) {
            return response()->json(['inWishlist' => $inWishlist, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
