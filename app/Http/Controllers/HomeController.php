<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\ServiceItem;
use App\Models\Technician;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::active()->where('is_featured', true)->with('category')->latest()->take(8)->get();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')->get();

        // Banner beranda: tiap gambar mengarah ke kategori produk yang sesuai.
        $categoryUrl = function (callable $match) use ($categories) {
            $category = $categories->first(fn ($c) => $match(Str::lower($c->name)));

            return route('products.index', array_filter(['kategori' => $category?->slug]));
        };

        $heroSlides = [
            [
                'title' => 'Laptop Gaming',
                'eyebrow' => 'Laptop Gaming',
                'heading' => 'Main lancar, kerja pun ngebut.',
                'text' => 'Pilihan laptop gaming dengan prosesor dan kartu grafis bertenaga, siap untuk game berat, desain, hingga editing video.',
                'points' => ['Prosesor & VGA bertenaga', 'Cocok untuk game dan kerja kreatif'],
                'image' => asset('images/banner/laptop-gaming.jpg'),
                'position' => '72% center',
                'url' => $categoryUrl(fn ($n) => str_contains($n, 'laptop') && str_contains($n, 'gaming')),
            ],
            [
                'title' => 'PC Rakitan & Desktop',
                'eyebrow' => 'PC Rakitan & Desktop',
                'heading' => 'Rakit sesuai kebutuhan, pas di kantong.',
                'text' => 'Susun komponen sesuai keinginan atau pilih desktop siap pakai untuk kantor, sekolah, gaming, maupun desain.',
                'points' => ['Komponen bisa dipilih', 'Disesuaikan dengan anggaran'],
                'image' => asset('images/banner/pc-rakitan.jpg'),
                'position' => '78% center',
                'url' => $categoryUrl(fn ($n) => str_contains($n, 'rakitan') || str_contains($n, 'desktop')),
            ],
            [
                'title' => 'Aksesori & Upgrade',
                'eyebrow' => 'Aksesori & Upgrade',
                'heading' => 'Lengkapi setup, tingkatkan performa.',
                'text' => 'Keyboard, mouse, headset, hingga upgrade RAM dan SSD supaya perangkatmu lebih cepat dan nyaman dipakai.',
                'points' => ['Perangkat jadi lebih cepat', 'Setup lebih nyaman'],
                'image' => asset('images/banner/aksesori-upgrade.jpg'),
                'position' => '68% center',
                'url' => $categoryUrl(fn ($n) => str_contains($n, 'aksesori') || str_contains($n, 'aksesoris') || str_contains($n, 'upgrade')),
            ],
        ];

        $featuredReviews = Review::visible()->where('is_featured', true)->with(['user', 'product'])->latest()->take(8)->get();

        return view('home', compact('featured', 'heroSlides', 'categories', 'featuredReviews'));
    }

    public function about()
    {
        return view('about', [
            'technicians' => Technician::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function services()
    {
        return view('services', [
            'items' => ServiceItem::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
