@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<div class="mb-6 flex items-center justify-between"><h1 class="text-2xl font-bold">Kategori Produk</h1><a class="btn btn-primary" href="{{ route('admin.kategori.create') }}">+ Tambah Kategori</a></div>
<div class="card overflow-x-auto">
    <table class="tbl">
        <tr><th>Nama</th><th>Slug</th><th>Jumlah produk</th><th>Status</th><th>Aksi</th></tr>
        @forelse ($categories as $c)
            <tr><td>{{ $c->name }}</td><td>{{ $c->slug }}</td><td>{{ $c->products_count }}</td><td><span class="chip">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td class="flex gap-3"><a class="underline" href="{{ route('admin.kategori.edit', $c) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.kategori.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="underline" type="submit">Hapus</button></form></td></tr>
        @empty
            <tr><td colspan="5">Belum ada kategori.</td></tr>
        @endforelse
    </table>
</div>
@endsection
