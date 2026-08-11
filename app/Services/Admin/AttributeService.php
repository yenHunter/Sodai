<?php

namespace App\Services\Admin;

use App\Models\Attribute;
use Illuminate\Support\Facades\Cache;

class AttributeService
{
    private const CACHE_KEY = 'active_attribute_keys';

    public function update(Attribute $attribute, array $data): Attribute
    {
        $attribute->update([
            'label'  => $data['label'],
            'status' => $data['status'],
        ]);

        Cache::forget(self::CACHE_KEY);

        return $attribute->fresh();
    }

    public function toggleStatus(Attribute $attribute): Attribute
    {
        $attribute->update([
            'status' => $attribute->status === 'active' ? 'inactive' : 'active',
        ]);

        Cache::forget(self::CACHE_KEY);

        return $attribute->fresh();
    }

    public function getAttributesList()
    {
        return Attribute::ordered()->get();
    }

    /**
     * Used by the Product create/edit form and details page to decide
     * which of the Color/Size/Weight field groups to render.
     */
    public function getActiveKeys(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return Attribute::active()->pluck('key')->toArray();
        });
    }
}