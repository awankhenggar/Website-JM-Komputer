<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReturnRequest extends Model
{
    public const STATUSES = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'refund_selesai' => 'Refund selesai',
    ];

    public const TYPES = [
        'batal' => 'Pembatalan pesanan',
        'retur' => 'Retur barang',
    ];

    protected $fillable = [
        'order_id', 'user_id', 'code', 'type', 'reason', 'description', 'photo',
        'status', 'admin_note', 'refund_amount', 'restocked',
    ];

    protected function casts(): array
    {
        return ['restocked' => 'boolean'];
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RTR'.now()->format('ymd').strtoupper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['ditolak', 'refund_selesai'], true);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
