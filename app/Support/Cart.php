<?php

namespace App\Support;

use App\Models\Product;

class Cart
{
    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function count(): int
    {
        return (int) array_sum(self::raw());
    }

    public static function items()
    {
        $cart = self::raw();
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->map(fn ($qty, $id) => ['product' => $products->get($id), 'qty' => $qty])
            ->filter(fn ($i) => $i['product'] && $i['product']->is_active)
            ->values();
    }

    public static function subtotal(): int
    {
        return (int) self::items()->sum(fn ($i) => $i['product']->price * $i['qty']);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
