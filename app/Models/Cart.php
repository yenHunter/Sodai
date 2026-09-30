<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getTotalValueAttribute(): float
    {
        return round($this->items->sum('subtotal'), 2);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }
}
