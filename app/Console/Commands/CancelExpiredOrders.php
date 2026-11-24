<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Batalkan pesanan yang belum dibayar melewati batas waktu dan kembalikan stok';

    public function handle(): int
    {
        $hours = max(1, (int) config('store.payment_hours', 24));

        $expired = Order::where('status', 'menunggu_bayar')
            ->where('created_at', '<', now()->subHours($hours))
            ->get();

        foreach ($expired as $order) {
            $order->cancelAndRestock();
        }

        $this->info($expired->count().' pesanan dibatalkan.');

        return self::SUCCESS;
    }
}
