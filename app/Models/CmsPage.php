<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    public const SLUGS = [
        'privacy-policy',
        'terms-conditions',
        'shipping-policy',
        'return-refund-policy',
    ];

    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'updated_by',
    ];

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public static function defaultTitleFor(string $slug): string
    {
        return match ($slug) {
            'privacy-policy' => 'Privacy Policy',
            'terms-conditions' => 'Terms & Conditions',
            'shipping-policy' => 'Shipping Policy',
            'return-refund-policy' => 'Return & Refund Policy',
            default => ucwords(str_replace('-', ' ', $slug)),
        };
    }

    /**
     * Always returns a page for a valid slug, creating an empty
     * draft on first visit so the admin edit screen never 404s.
     */
    public static function findOrCreateBySlug(string $slug): self
    {
        return static::firstOrCreate(
            ['slug' => $slug],
            ['title' => static::defaultTitleFor($slug)]
        );
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
