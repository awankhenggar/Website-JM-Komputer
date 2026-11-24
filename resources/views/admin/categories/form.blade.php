@extends('layouts.admin')
@section('title', $category->exists ? 'Ubah Kategori' : 'Tambah Kategori')
@section('content')
<h1 class="mb-6 text-2xl font-bold">{{ $category->exists ? 'Ubah' : 'Tambah' }} Kategori</h1>
<form method="POST" action="{{ $category->exists ? route('admin.kategori.update', $category) : route('admin.kategori.store') }}" class="card grid max-w-xl gap-4">
    @csrf
    @if ($category->exists) @method('PUT') @endif
    <x-field name="name" label="Nama kategori" :value="$category->name" required />
    <x-field name="description" label="Deskripsi" type="textarea" :value="$category->description" />
    <x-check name="is_active" label="Tampilkan di toko" :checked="(bool) $category->is_active" />
    <div class="flex gap-3"><button class="btn btn-primary" type="submit">Simpan</button><a class="btn" href="{{ route('admin.kategori.index') }}">Batal</a></div>
</form>
@endsection
