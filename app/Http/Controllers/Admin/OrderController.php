<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('q'), function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('code', 'like', "%{$v}%")->orWhere('recipient_name', 'like', "%{$v}%");
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status === 'batal') {
            return back()->with('error', 'Pesanan yang dibatalkan tidak dapat diubah.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'cancellation_note' => ['required_if:status,batal', 'nullable', 'string', 'min:5', 'max:1000'],
        ]);

        if ($data['status'] === 'batal') {
            $order->cancelAndRestock($data['cancellation_note']);

            return back()->with('success', 'Pesanan dibatalkan, catatan disimpan, dan stok dikembalikan.');
        }

        $order->fill([
            'status' => $data['status'],
            'shipping_cost' => $data['shipping_cost'] ?? 0,
            'tracking_number' => $data['tracking_number'] ?? null,
        ]);
        $order->total = $order->subtotal + $order->shipping_cost;

        if ($data['status'] === 'dibayar' && ! $order->paid_at) {
            $order->paid_at = now();
        }

        if ($data['status'] === 'selesai' && ! $order->completed_at) {
            $order->completed_at = now();
        }

        $order->save();

        return back()->with('success', 'Pesanan diperbarui.');
    }
}
