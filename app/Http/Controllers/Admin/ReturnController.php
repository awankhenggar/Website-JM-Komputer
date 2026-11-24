<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = ReturnRequest::with(['order', 'user'])
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.returns.index', compact('returns'));
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load('order.items', 'user');

        return view('admin.returns.show', ['ret' => $returnRequest]);
    }

    public function update(Request $request, ReturnRequest $returnRequest)
    {
        if ($returnRequest->isFinal()) {
            return back()->with('error', 'Pengajuan ini sudah final dan tidak bisa diubah.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ReturnRequest::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:1000'],
            'refund_amount' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($data['status'] === 'refund_selesai' && empty($data['refund_amount'])) {
            return back()->withInput()->withErrors(['refund_amount' => 'Isi nominal refund untuk status Refund selesai.']);
        }

        DB::transaction(function () use ($returnRequest, $data, $request) {
            $order = $returnRequest->order()->with('items')->first();

            if (in_array($data['status'], ['disetujui', 'refund_selesai'], true)) {
                if ($returnRequest->type === 'batal' && $order->status !== 'batal') {
                    $order->cancelAndRestock();
                } elseif ($returnRequest->type === 'retur' && $request->boolean('restock') && ! $returnRequest->restocked) {
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            Product::whereKey($item->product_id)->increment('stock', $item->qty);
                        }
                    }
                    $returnRequest->restocked = true;
                }
            }

            $returnRequest->fill([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
                'refund_amount' => $data['refund_amount'] ?? null,
            ])->save();
        });

        return back()->with('success', 'Pengajuan diperbarui.');
    }
}
