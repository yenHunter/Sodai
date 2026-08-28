<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    public const SLUGS = [
        'about',
        'privacy-policy',
        'terms-conditions',
        'shipping-policy',
        'return-refund-policy',
    ];

    // Pages that show an image field on the admin edit screen.
    // Policy pages don't need one; keeping this explicit avoids an
    // unused upload control cluttering those forms.
    public const SLUGS_WITH_IMAGE = ['about'];

    protected $fillable = [
        'slug',
        'title',
        'content',
        'image',
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
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    public function supportsImage(): bool
    {
        return in_array($this->slug, self::SLUGS_WITH_IMAGE, true);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public static function defaultTitleFor(string $slug): string
    {
        return match ($slug) {
            'about' => 'About Us',
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
