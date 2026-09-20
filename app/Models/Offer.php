<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'category_id',
        'image',
        'is_active',
        'sort_order',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    /**
     * "Shop Now" destination: an explicit button_url always wins (lets an
     * admin point at any arbitrary page), otherwise falls back to the
     * linked category, otherwise the storefront's generic products page.
     */
    public function getResolvedButtonUrlAttribute(): string
    {
        if ($this->button_url) {
            return $this->button_url;
        }

        if ($this->category) {
            return route('visitor.products.category', $this->category->slug);
        }

        return route('visitor.products.index');
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Expired';
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Scheduled';
        }

        return 'Active';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_label) {
            'Active' => 'bg-success',
            'Scheduled' => 'bg-info',
            'Expired' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeCurrentlyValid($query)
    {
        $now = now();

        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }
}
