<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@jmkomputer.test'], [
            'name' => 'Admin JM Komputer', 'phone' => '081200000001', 'password' => 'password', 'role' => 'admin',
        ]);
        User::firstOrCreate(['email' => 'teknisi@jmkomputer.test'], [
            'name' => 'Teknisi Contoh', 'phone' => '081200000002', 'password' => 'password', 'role' => 'teknisi',
        ]);
        User::firstOrCreate(['email' => 'pelanggan@jmkomputer.test'], [
            'name' => 'Pelanggan Contoh', 'phone' => '081200000003', 'password' => 'password', 'role' => 'pelanggan',
        ]);

        $cats = [];
        foreach (['Laptop Kerja & Kuliah', 'Laptop Gaming', 'PC Rakitan & Desktop', 'Aksesori & Upgrade'] as $name) {
            $cats[] = Category::firstOrCreate(['name' => $name], ['slug' => Category::makeSlug($name), 'is_active' => true]);
        }

        $samples = [
            ['Laptop Kerja 14 inci', 0, 7500000, 10, true],
            ['Laptop Kuliah Ringan', 0, 6200000, 6, true],
            ['Laptop Gaming 15 inci', 1, 14500000, 4, true],
            ['Laptop Gaming Tipis', 1, 17800000, 2, false],
            ['PC Rakitan Kantor', 2, 5400000, 5, true],
            ['PC Rakitan Gaming', 2, 11900000, 3, true],
            ['SSD 512 GB', 3, 650000, 20, false],
            ['RAM DDR4 8 GB', 3, 420000, 15, false],
        ];

        foreach ($samples as [$name, $ci, $price, $stock, $featured]) {
            Product::firstOrCreate(['name' => $name], [
                'category_id' => $cats[$ci]->id,
                'slug' => Product::makeSlug($name),
                'description' => 'Produk contoh. Ganti dengan deskripsi asli dari panel admin.',
                'specs' => "Prosesor: [isi]\nRAM: [isi]\nPenyimpanan: [isi]",
                'price' => $price,
                'stock' => $stock,
                'weight_gram' => 2000,
                'is_active' => true,
                'is_featured' => $featured,
            ]);
        }

        $this->call(ContentSeeder::class);
    }
}
