@extends('layouts.app')
@section('title', 'Tentang '.config('store.name'))
@section('content')
<h1 class="mb-4 text-3xl font-bold">Tentang {{ config('store.name') }}</h1>
<div class="card max-w-3xl">
    <p class="whitespace-pre-line text-sm leading-relaxed">{{ config('store.about') }}</p>
    <p class="mt-4 text-sm"><b>Alamat:</b> {{ config('store.address') }}<br><b>Jam buka:</b> {{ config('store.hours') }}</p>
    <div class="mt-4 flex gap-3"><a class="btn btn-primary" href="{{ route('products.index') }}">Lihat Produk</a><a class="btn" href="{{ route('contact') }}">Kontak &amp; Lokasi</a></div>
</div>

@if ($technicians->isNotEmpty())
    <h2 class="mb-4 mt-10 text-2xl font-bold">Tim Teknisi</h2>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ($technicians as $t)
            <div class="card flex flex-col gap-1">
                @if ($t->photo_url)
                    <img src="{{ $t->photo_url }}" alt="Foto {{ $t->name }}" class="h-40 w-full rounded-lg object-cover">
                @else
                    <div class="flex h-40 items-center justify-center rounded-lg bg-stone-200 text-sm text-stone-600">Foto</div>
                @endif
                <b>{{ $t->name }}</b>
                @if ($t->title)<span class="text-xs text-stone-600">{{ $t->title }}</span>@endif
                @if ($t->expertise)<span class="text-sm">{{ $t->expertise }}</span>@endif
            </div>
        @endforeach
    </div>
@endif
@endsection
