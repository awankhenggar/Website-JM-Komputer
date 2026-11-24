<?php

namespace Database\Seeders;

use App\Models\ServiceItem;
use App\Models\Technician;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Ganti layar', 'Penggantian layar laptop yang pecah atau bergaris.'],
            ['Ganti baterai / keyboard', 'Penggantian baterai drop atau keyboard rusak.'],
            ['Upgrade RAM / SSD', 'Tambah RAM atau ganti HDD ke SSD agar lebih cepat.'],
            ['Install ulang OS & software', 'Instal ulang Windows beserta aplikasi yang dibutuhkan.'],
            ['Pembersihan & thermal paste', 'Bersihkan debu dan ganti thermal paste agar tidak panas.'],
            ['Mati total / motherboard', 'Pemeriksaan dan perbaikan laptop atau PC yang tidak menyala.'],
        ];

        foreach ($items as $i => [$name, $desc]) {
            ServiceItem::firstOrCreate(['name' => $name], ['description' => $desc, 'sort_order' => $i + 1, 'is_active' => true]);
        }

        Technician::firstOrCreate(['name' => 'Teknisi Contoh'], [
            'title' => 'Teknisi Utama', 'expertise' => 'Laptop, PC, dan upgrade perangkat', 'sort_order' => 1, 'is_active' => true,
        ]);
    }
}
