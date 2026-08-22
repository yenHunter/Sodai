<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = [
        'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded',
    ];

    public const EDITABLE_STATUSES = ['pending', 'confirmed'];

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'discount_amount',
        'shipping_charge',
        'tax_amount',
        'total_amount',
        'coupon_code',
        'coupon_id',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'notes',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES);
    }

    public function isCancellable(): bool
    {
        return ! in_array($this->status, ['delivered', 'cancelled', 'refunded']);
    }

    public function isDeletable(): bool
    {
        return in_array($this->status, ['cancelled', 'refunded']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-soft-warning',
            'confirmed' => 'badge-soft-info',
            'processing' => 'badge-soft-primary',
            'shipped' => 'badge-soft-info',
            'delivered' => 'badge-soft-success',
            'cancelled' => 'badge-soft-danger',
            'refunded' => 'badge-soft-secondary',
            default => 'badge-soft-secondary',
        };
    }

    public function getRefundedAmountAttribute(): float
    {
        return (float) $this->refunds()
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
                ->orWhere('shipping_name', 'like', "%{$search}%")
                ->orWhere('shipping_email', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
