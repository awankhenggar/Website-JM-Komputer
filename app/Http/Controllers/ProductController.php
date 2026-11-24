<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($slug = $request->query('kategori')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
        }

        $sort = $request->query('urut');
        if ($sort === 'termurah') {
            $query->orderBy('price');
        } elseif ($sort === 'termahal') {
            $query->orderByDesc('price');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)->get();

        $reviewCount = $product->reviews()->visible()->count();
        $avgRating = $reviewCount ? round((float) $product->reviews()->visible()->avg('rating'), 1) : null;
        $reviews = $product->reviews()->visible()->with('user')->latest()->take(5)->get();

        return view('products.show', compact('product', 'related', 'reviewCount', 'avgRating', 'reviews'));
    }
}
