<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        if ($product->stock < 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Stok produk habis.'], 422);
            }

            return back()->with('error', 'Stok produk habis.');
        }

        $qty = max(1, (int) $request->input('qty', 1));
        $cart = Cart::raw();
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $qty, $product->stock);
        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'count' => Cart::count(),
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product)
    {
        $cart = Cart::raw();

        if (isset($cart[$product->id])) {
            $qty = (int) $request->input('qty', 1);
            $cart[$product->id] = max(1, min($qty, max(1, $product->stock)));
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function updateAll(Request $request)
    {
        $validated = $request->validate([
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:1'],
        ]);

        $cart = Cart::raw();
        $products = Product::whereIn('id', array_keys($validated['qty']))
            ->get()
            ->keyBy('id');

        foreach ($validated['qty'] as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product || ! $product->is_active || $product->stock < 1) {
                unset($cart[$productId]);
                continue;
            }

            $cart[$productId] = min((int) $quantity, $product->stock);
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Jumlah produk di keranjang berhasil diperbarui.');
    }

    public function remove(Product $product)
    {
        $cart = Cart::raw();
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
