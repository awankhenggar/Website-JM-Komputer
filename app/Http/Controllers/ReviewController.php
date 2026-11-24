<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $total = Review::visible()->count();
        $avg = $total ? round((float) Review::visible()->avg('rating'), 1) : 0;
        $dist = Review::visible()->selectRaw('rating, COUNT(*) as c')->groupBy('rating')->pluck('c', 'rating');

        $reviews = Review::visible()->with(['user', 'product'])
            ->when($request->query('bintang'), fn ($q, $v) => $q->where('rating', (int) $v))
            ->when($request->query('produk'), fn ($q, $v) => $q->where('product_id', (int) $v))
            ->latest()->paginate(10)->withQueryString();

        $products = Product::whereIn('id', Review::visible()->whereNotNull('product_id')->pluck('product_id'))
            ->orderBy('name')->get(['id', 'name']);

        return view('reviews.index', compact('reviews', 'total', 'avg', 'dist', 'products'));
    }

    public function create(OrderItem $orderItem)
    {
        $this->authorizeItem($orderItem);

        return view('reviews.create', ['item' => $orderItem->load('order', 'product')]);
    }

    public function store(Request $request, OrderItem $orderItem)
    {
        $this->authorizeItem($orderItem);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('reviews', 'public');
        }

        Review::create($data + [
            'user_id' => auth()->id(),
            'product_id' => $orderItem->product_id,
            'order_item_id' => $orderItem->id,
        ]);

        return redirect()->route('account.orders.show', $orderItem->order_id)
            ->with('success', 'Terima kasih, ulasan Anda sudah tampil.');
    }

    private function authorizeItem(OrderItem $orderItem): void
    {
        $orderItem->loadMissing('order');

        abort_unless($orderItem->order && $orderItem->order->user_id === auth()->id(), 404);
        abort_unless($orderItem->product_id, 404);
        abort_unless($orderItem->order->status === 'selesai', 403, 'Ulasan hanya bisa ditulis setelah pesanan selesai.');
        abort_if($orderItem->review()->exists(), 403, 'Anda sudah menulis ulasan untuk produk ini.');
    }
}
