<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'menunggu_bayar' => 'Menunggu pembayaran',
        'dibayar' => 'Dibayar',
        'dikemas' => 'Dikemas',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'batal' => 'Dibatalkan',
    ];

    public const PAID = ['dibayar', 'dikemas', 'dikirim', 'selesai'];

    protected $fillable = [
        'user_id', 'code', 'status', 'subtotal', 'shipping_cost', 'total', 'recipient_name',
        'phone', 'address', 'city', 'notes', 'payment_method', 'payment_proof',
        'tracking_number', 'paid_at', 'completed_at', 'cancellation_note',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public static function generateCode(): string
    {
        do {
            $code = 'JM'.now()->format('ymd').strtoupper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function hasOpenReturn(): bool
    {
        return $this->returnRequests()->whereIn('status', ['baru', 'diproses', 'disetujui'])->exists();
    }

    public function allowedReturnTypes(): array
    {
        if ($this->hasOpenReturn()) {
            return [];
        }

        $types = [];

        if (in_array($this->status, ['dibayar', 'dikemas'], true)) {
            $types[] = 'batal';
        }

        if ($this->status === 'selesai') {
            $days = (int) config('store.return_days', 3);
            $since = $this->completed_at ?? $this->updated_at;

            if ($since && $since->copy()->addDays($days)->isFuture()) {
                $types[] = 'retur';
            }
        }

        return $types;
    }

    public function cancelAndRestock(?string $reason = null): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->qty);
                }
            }
            $this->update([
                'status' => 'batal',
                'cancellation_note' => $reason,
            ]);
        });
    }
}
