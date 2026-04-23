<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) return redirect()->route('login');

        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($product->reviews()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Vous avez déjà évalué ce produit.');
        }

        $product->reviews()->create([
            'user_id' => Auth::id(),
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Évaluation ajoutée.');
    }

    public function destroy(Review $review)
    {
       
        if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $review->delete();
        return back()->with('success', 'Évaluation supprimée.');
    }
}