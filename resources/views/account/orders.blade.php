@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<h1 class="mb-6 text-3xl font-bold">Pesanan Saya</h1>
<div class="card overflow-x-auto">
    <table class="tbl">
        <tr><th>No. pesanan</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr>
        @forelse ($orders as $o)
            <tr><td>{{ $o->code }}</td><td>{{ $o->created_at->format('d/m/Y H:i') }}</td><td>@rp($o->total)</td><td><x-status :model="$o" /></td><td><a class="underline" href="{{ route('account.orders.show', $o) }}">Detail</a></td></tr>
        @empty
            <tr><td colspan="5">Belum ada pesanan.</td></tr>
        @endforelse
    </table>
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
