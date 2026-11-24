@extends('layouts.app')
@section('title', 'Servis Saya')
@section('content')
<div class="mb-6 flex items-center justify-between"><h1 class="text-3xl font-bold">Servis Saya</h1><a class="btn btn-primary" href="{{ route('booking.create') }}">Booking Servis</a></div>
<div class="card overflow-x-auto">
    <table class="tbl">
        <tr><th>No. servis</th><th>Perangkat</th><th>Status</th><th>Tanggal</th><th></th></tr>
        @forelse ($services as $s)
            <tr><td>{{ $s->code }}</td><td>{{ $s->brand_model }}</td><td><x-status :model="$s" /></td><td>{{ $s->created_at->format('d/m/Y') }}</td><td><a class="underline" href="{{ route('account.services.show', $s) }}">Lihat</a></td></tr>
        @empty
            <tr><td colspan="5">Belum ada servis.</td></tr>
        @endforelse
    </table>
    <div class="mt-4">{{ $services->links() }}</div>
</div>
@endsection
