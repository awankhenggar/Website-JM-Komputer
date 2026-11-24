@extends('layouts.app')
@section('title', 'Pesanan '.$order->code)
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3"><h1 class="text-3xl font-bold">Pesanan {{ $order->code }}</h1><x-status :model="$order" /></div>
@if ($order->status === 'batal' && $order->cancellation_note)
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm">
        <b class="text-rose-800">Catatan pembatalan dari admin</b>
        <p class="mt-2 whitespace-pre-line text-rose-900">{{ $order->cancellation_note }}</p>
    </div>
@endif
<div class="grid gap-6 md:grid-cols-3">
    <div class="card md:col-span-2">
        <b>Item pesanan</b>
        <table class="tbl mt-2">
            <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th></th></tr>
            @foreach ($order->items as $i)
                <tr><td>{{ $i->name }}</td><td>{{ $i->qty }}</td><td>@rp($i->price * $i->qty)</td>
                    <td>@if ($order->status === 'selesai' && $i->product_id)@if ($i->review)<span class="chip">Sudah diulas</span>@else<a class="underline" href="{{ route('account.reviews.create', $i) }}">Beri ulasan</a>@endif @endif</td></tr>
            @endforeach
        </table>
        <div class="mt-3 flex justify-between text-sm"><span>Subtotal</span><span>@rp($order->subtotal)</span></div>
        <div class="flex justify-between text-sm"><span>Ongkir</span><span>@rp($order->shipping_cost)</span></div>
        <div class="flex justify-between border-t border-stone-300 pt-2"><b>Total</b><b>@rp($order->total)</b></div>
    </div>
    <div class="flex flex-col gap-4">
        <div class="card text-sm"><b>Pengiriman</b><br>{{ $order->recipient_name }} ({{ $order->phone }})<br>{{ $order->address }}, {{ $order->city }}<br>@if ($order->tracking_number)Resi: <b>{{ $order->tracking_number }}</b>@endif</div>
        @if ($order->status === 'menunggu_bayar')
            <div class="card text-sm">
                <b>Pembayaran</b>
                <p class="mt-1">Transfer ke: {{ config('store.bank_info') }}</p>
                <p class="mt-1">Batas bayar: {{ $order->created_at->copy()->addHours((int) config('store.payment_hours', 24))->format('d/m/Y H:i') }}. Total akhir termasuk ongkir yang ditentukan toko.</p>
                <form method="POST" action="{{ route('account.orders.proof', $order) }}" enctype="multipart/form-data" class="mt-3 grid gap-2">
                    @csrf
                    <x-field name="proof" label="Unggah bukti transfer" type="file" accept="image/*" />
                    <button class="btn btn-primary" type="submit">Kirim Bukti</button>
                </form>
                @if ($order->payment_proof)<p class="mt-2 text-xs">Bukti sudah terkirim, menunggu pemeriksaan admin.</p>@endif
                <form method="POST" action="{{ route('account.orders.cancel', $order) }}" class="mt-3" onsubmit="return confirm('Batalkan pesanan ini?')">@csrf<button class="btn w-full" type="submit">Batalkan Pesanan</button></form>
            </div>
        @endif
        @php
            $returnTypes = $order->allowedReturnTypes();
            $returns = $order->returnRequests()->latest()->get();
        @endphp
        @if ($returns->isNotEmpty() || ! empty($returnTypes))
            <div class="card text-sm">
                <b>Pembatalan &amp; retur</b>
                @foreach ($returns as $r)
                    <p class="mt-2">{{ $r->code }} &middot; {{ $r->typeLabel() }}: <x-status :model="$r" />@if ($r->admin_note)<br><span class="text-xs">Catatan toko: {{ $r->admin_note }}</span>@endif @if ($r->refund_amount)<br><span class="text-xs">Refund: @rp($r->refund_amount)</span>@endif</p>
                @endforeach
                @if (! empty($returnTypes))
                    <a class="btn mt-3 w-full" href="{{ route('account.returns.create', $order) }}">Ajukan Pembatalan / Retur</a>
                @endif
            </div>
        @endif
        <a class="btn" target="_blank" rel="noopener" href="https://wa.me/{{ config('store.whatsapp') }}?text={{ urlencode('Halo, saya ingin bertanya tentang pesanan '.$order->code) }}">Konsultasi via WhatsApp</a>
    </div>
</div>
@endsection
