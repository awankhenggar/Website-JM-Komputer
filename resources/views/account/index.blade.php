@extends('layouts.app')
@section('title', 'Akun Saya')
@section('content')
<h1 class="mb-6 text-3xl font-bold">Halo, {{ auth()->user()->name }}</h1>
<div class="grid gap-6 md:grid-cols-2">
    <div class="card">
        <div class="mb-3 flex items-center justify-between"><b>Pesanan terbaru</b><a class="text-sm underline" href="{{ route('account.orders') }}">Semua pesanan</a></div>
        @forelse ($orders as $o)
            <div class="flex items-center justify-between border-b border-stone-200 py-2 text-sm">
                <a class="underline" href="{{ route('account.orders.show', $o) }}">{{ $o->code }}</a><x-status :model="$o" /><span>@rp($o->total)</span>
            </div>
        @empty
            <p class="text-sm">Belum ada pesanan. <a class="underline" href="{{ route('products.index') }}">Lihat produk</a></p>
        @endforelse
    </div>
    <div class="card">
        <div class="mb-3 flex items-center justify-between"><b>Servis saya</b><a class="text-sm underline" href="{{ route('account.services') }}">Semua servis</a></div>
        @forelse ($services as $s)
            <div class="flex items-center justify-between border-b border-stone-200 py-2 text-sm">
                <a class="underline" href="{{ route('account.services.show', $s) }}">{{ $s->code }}</a><span>{{ $s->brand_model }}</span><x-status :model="$s" />
            </div>
        @empty
            <p class="text-sm">Belum ada servis. <a class="underline" href="{{ route('booking.create') }}">Booking servis</a></p>
        @endforelse
    </div>
</div>
@endsection
