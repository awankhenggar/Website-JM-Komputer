<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('account.index', [
            'orders' => $user->orders()->latest()->take(5)->get(),
            'services' => $user->serviceRequests()->latest()->take(5)->get(),
        ]);
    }

    public function orders()
    {
        return view('account.orders', ['orders' => auth()->user()->orders()->latest()->paginate(10)]);
    }

    public function orderShow(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 404);
        $order->load('items');

        return view('account.order-show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 404);

        if ($order->status !== 'menunggu_bayar') {
            return back()->with('error', 'Bukti pembayaran hanya bisa diunggah untuk pesanan yang menunggu pembayaran.');
        }

        $request->validate(['proof' => ['required', 'image', 'max:3072']]);
        $order->update(['payment_proof' => $request->file('proof')->store('proofs', 'public')]);

        return back()->with('success', 'Bukti pembayaran terkirim. Admin akan memeriksanya.');
    }

    public function cancelOrder(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 404);

        if ($order->status !== 'menunggu_bayar') {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan sendiri. Hubungi toko.');
        }

        $order->cancelAndRestock();

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function services()
    {
        return view('account.services', [
            'services' => auth()->user()->serviceRequests()->latest()->paginate(10),
        ]);
    }

    public function serviceShow(ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->user_id === auth()->id(), 404);
        $serviceRequest->load('updates', 'order');

        return view('account.service-show', ['service' => $serviceRequest]);
    }

    public function serviceDecision(Request $request, ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->user_id === auth()->id(), 404);

        if ($serviceRequest->status !== 'menunggu_persetujuan') {
            return back()->with('error', 'Estimasi biaya tidak sedang menunggu keputusan Anda.');
        }

        $decision = $request->validate(['decision' => ['required', 'in:setuju,tolak']])['decision'];

        if ($decision === 'setuju') {
            $serviceRequest->update(['status' => 'dikerjakan']);
            $serviceRequest->log('dikerjakan', 'Pelanggan menyetujui estimasi biaya.');
            $message = 'Estimasi disetujui. Perangkat Anda akan dikerjakan.';
        } else {
            $serviceRequest->update(['status' => 'dibatalkan']);
            $serviceRequest->log('dibatalkan', 'Pelanggan menolak estimasi biaya.');
            $message = 'Estimasi ditolak. Silakan ambil perangkat Anda di toko.';
        }

        return back()->with('success', $message);
    }
}
