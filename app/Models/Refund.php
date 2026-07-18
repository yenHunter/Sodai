<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'refund_number',
        'order_id',
        'user_id',
        'amount',
        'reason',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public function isEditable(): bool
    {
        return $this->status === 'pending';
    }

    public function isDeletable(): bool
    {
        return $this->status !== 'approved';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'badge-soft-warning',
            'approved' => 'badge-soft-success',
            'rejected' => 'badge-soft-danger',
            default    => 'badge-soft-secondary',
        };
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('refund_number', 'like', "%{$search}%")
              ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
              ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        });
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}