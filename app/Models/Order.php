<?php

namespace App\Models;

use App\Enums\OrderStatus; // Ensure you created this Enum in Step 1
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_amount', 'invoice_number'];

    protected $casts = [
        'status' => OrderStatus::class, // Auto-cast string to Enum
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
