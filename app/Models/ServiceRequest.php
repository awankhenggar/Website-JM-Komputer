<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    public const STATUSES = [
        'booking' => 'Booking diterima',
        'diagnosa' => 'Diagnosa',
        'menunggu_persetujuan' => 'Menunggu persetujuan',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai - menunggu pembayaran',
        'lunas' => 'Selesai - lunas',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'user_id', 'order_id', 'code', 'device_type', 'brand_model', 'complaint',
        'service_type', 'handover', 'visit_date', 'status', 'diagnosis',
        'estimate_cost', 'final_cost', 'admin_notes',
    ];

    protected function casts(): array
    {
        return ['visit_date' => 'date'];
    }

    public static function generateCode(): string
    {
        do {
            $code = 'SRV'.now()->format('ymd').strtoupper(Str::random(4));
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function updates()
    {
        return $this->hasMany(ServiceUpdate::class)->latest();
    }

    public function log(string $status, ?string $note = null): void
    {
        $this->updates()->create(['status' => $status, 'note' => $note]);
    }
}
