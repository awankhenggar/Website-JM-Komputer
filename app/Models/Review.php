<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_item_id', 'rating', 'comment', 'photo', 'is_hidden', 'is_featured', 'admin_reply'];

    protected function casts(): array
    {
        return ['is_hidden' => 'boolean', 'is_featured' => 'boolean', 'rating' => 'integer'];
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }

    public function stars(): string
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }
}
