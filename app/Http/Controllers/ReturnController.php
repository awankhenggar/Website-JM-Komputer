<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function create(Order $order)
    {
        $types = $this->types($order);

        return view('returns.create', compact('order', 'types'));
    }

    public function store(Request $request, Order $order)
    {
        $types = $this->types($order);

        $data = $request->validate([
            'type' => ['required', Rule::in($types)],
            'reason' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => [Rule::requiredIf($request->input('type') === 'retur'), 'image', 'max:3072'],
        ]);

        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('returns', 'public');
        }

        ReturnRequest::create($data + [
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'code' => ReturnRequest::generateCode(),
            'status' => 'baru',
        ]);

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Pengajuan terkirim. Kami akan menghubungi Anda.');
    }

    private function types(Order $order): array
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $types = $order->allowedReturnTypes();
        abort_if(empty($types), 403, 'Pesanan ini tidak bisa diajukan pembatalan atau retur saat ini.');

        return $types;
    }
}
