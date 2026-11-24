<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $selectedIds = collect($request->input('selected', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        $cartItems = Cart::items();
        $items = $cartItems->filter(
            fn ($item) => $selectedIds->contains((int) $item['product']->id)
        )->values();

        if ($items->count() !== $selectedIds->count()) {
            return redirect()->route('cart.index')->with('error', 'Ada produk pilihan yang sudah tidak tersedia di keranjang. Silakan pilih ulang.');
        }

        return view('checkout.show', [
            'items' => $items,
            'subtotal' => $items->sum(fn ($item) => $item['product']->price * $item['qty']),
            'user' => auth()->user(),
            'selectedIds' => $selectedIds,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct'],
        ]);

        $cart = Cart::raw();
        $selectedIds = collect($data['selected'])->map(fn ($id) => (int) $id)->unique();

        if ($selectedIds->isEmpty() || $selectedIds->contains(fn ($id) => ! array_key_exists($id, $cart))) {
            return redirect()->route('cart.index')->with('error', 'Pilihan produk tidak valid. Silakan pilih ulang.');
        }

        $checkoutCart = collect($cart)
            ->only($selectedIds->all())
            ->all();

        $order = DB::transaction(function () use ($data, $checkoutCart) {
            $products = Product::whereIn('id', array_keys($checkoutCart))
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lines = [];

            foreach ($checkoutCart as $id => $qty) {
                $product = $products->get($id);

                if (! $product || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok '.($product->name ?? 'produk').' tidak mencukupi. Perbarui keranjang Anda.',
                    ]);
                }

                $subtotal += $product->price * $qty;
                $lines[] = [$product, $qty];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'code' => Order::generateCode(),
                'status' => 'menunggu_bayar',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'payment_method' => 'transfer',
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as [$product, $qty]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                ]);
                $product->decrement('stock', $qty);
            }

            return $order;
        });

        $remainingCart = Cart::raw();
        foreach ($selectedIds as $id) {
            unset($remainingCart[$id]);
        }
        session(['cart' => $remainingCart]);

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Pesanan dibuat. Selesaikan pembayaran dalam '.config('store.payment_hours', 24).' jam.');
    }
}
