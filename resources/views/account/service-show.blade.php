@extends('layouts.app')
@section('title', 'Servis '.$service->code)
@section('content')
@php
    $flow = ['booking', 'diagnosa', 'menunggu_persetujuan', 'dikerjakan', 'selesai', 'lunas'];
    $pos = array_search($service->status, $flow, true);
@endphp
<div class="mb-4"><h1 class="text-3xl font-bold">Servis {{ $service->code }}</h1><p class="text-sm text-stone-600">{{ $service->brand_model }} &middot; dibuat {{ $service->created_at->format('d/m/Y') }}</p></div>

@if ($service->status === 'dibatalkan')
    <div class="card mb-6"><span class="chip">Dibatalkan</span></div>
@else
    <div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-6">
        @foreach ($flow as $i => $st)
            <div class="rounded-lg border p-3 text-sm {{ $pos !== false && $i < $pos ? 'bg-brand text-white' : '' }} {{ $i === $pos ? 'border-4 border-brand font-bold' : 'border-stone-300' }}">{{ ($i + 1).'. '.\App\Models\ServiceRequest::STATUSES[$st] }}</div>
        @endforeach
    </div>
@endif

<div class="grid gap-6 md:grid-cols-2">
    <div class="flex flex-col gap-4">
        <div class="card text-sm"><b>Keluhan</b><p class="mt-1 whitespace-pre-line">{{ $service->complaint }}</p>
            <p class="mt-2 text-xs text-stone-600">Jenis: {{ $service->service_type === 'garansi' ? 'Klaim garansi' : 'Servis umum' }} &middot; Penyerahan: {{ $service->handover === 'datang' ? 'Datang ke toko' : 'Kirim via kurir' }}@if ($service->visit_date) &middot; Jadwal: {{ $service->visit_date->format('d/m/Y') }}@endif</p>
        </div>

        @if ($service->diagnosis || $service->estimate_cost)
            <div class="card text-sm">
                <b>Hasil diagnosa &amp; estimasi biaya</b>
                <p class="mt-1 whitespace-pre-line">{{ $service->diagnosis }}</p>
                @if ($service->estimate_cost)<p class="mt-2">Estimasi biaya: <b>@rp($service->estimate_cost)</b></p>@endif
                @if ($service->status === 'menunggu_persetujuan')
                    <form method="POST" action="{{ route('account.services.decision', $service) }}" class="mt-3 flex gap-3">
                        @csrf
                        <button class="btn btn-primary" name="decision" value="setuju" type="submit">Setujui Estimasi</button>
                        <button class="btn" name="decision" value="tolak" type="submit" onclick="return confirm('Tolak estimasi dan batalkan servis?')">Tolak</button>
                    </form>
                @endif
            </div>
        @endif

        @if (in_array($service->status, ['selesai', 'lunas'], true))
            <div class="card border-2 border-dashed text-sm">
                <b>Tagihan akhir</b>
                <p class="mt-1 text-xl font-bold">@rp($service->final_cost)</p>
                @if ($service->status === 'selesai')
                    <p class="mt-1">Transfer ke: {{ config('store.bank_info') }}, lalu ambil perangkat di toko. Admin menandai lunas setelah pembayaran diterima.</p>
                @else
                    <span class="chip mt-1">Lunas</span>
                @endif
            </div>
        @endif
        <a class="btn" target="_blank" rel="noopener" href="https://wa.me/{{ config('store.whatsapp') }}?text={{ urlencode('Halo, saya ingin bertanya tentang servis '.$service->code) }}">Konsultasi via WhatsApp</a>
    </div>
    <div class="card text-sm">
        <b>Riwayat status</b>
        @foreach ($service->updates as $u)
            <div class="mt-2 border-b border-stone-200 pb-2"><span class="text-xs text-stone-600">{{ $u->created_at->format('d/m/Y H:i') }}</span><br><b>{{ \App\Models\ServiceRequest::STATUSES[$u->status] ?? $u->status }}</b>@if ($u->note)<br>{{ $u->note }}@endif</div>
        @endforeach
    </div>
</div>
@endsection
