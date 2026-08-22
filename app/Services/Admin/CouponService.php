<?php

namespace App\Services\Admin;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CouponService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Coupon
    {
        return DB::transaction(function () use ($data) {
            return Coupon::create([
                'code' => Str::upper(trim($data['code'])),
                'type' => $data['type'],
                'value' => $data['value'],
                'minimum_order_amount' => $data['minimum_order_amount'] ?? 0,
                'maximum_discount' => $data['maximum_discount'] ?? null,
                'usage_limit' => $data['usage_limit'] ?? null,
                'usage_per_user' => $data['usage_per_user'] ?? 1,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Coupon $coupon, array $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $coupon->update([
                'code' => Str::upper(trim($data['code'])),
                'type' => $data['type'],
                'value' => $data['value'],
                'minimum_order_amount' => $data['minimum_order_amount'] ?? 0,
                'maximum_discount' => $data['maximum_discount'] ?? null,
                'usage_limit' => $data['usage_limit'] ?? null,
                'usage_per_user' => $data['usage_per_user'] ?? 1,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            return $coupon->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Coupon $coupon): bool
    {
        if ($coupon->used_count > 0) {
            throw new \Exception('Cannot delete a coupon that has already been used in orders.');
        }

        return $coupon->delete();
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Coupon $coupon): Coupon
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return $coupon->fresh();
    }

    // ─────────────────────────────────────────────
    // RESOLVE IS_ACTIVE
    // Handles: 'active', 'inactive', true, false, 1, 0
    // ─────────────────────────────────────────────

    private function resolveIsActive(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            return strtolower($value) === 'active';
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getCouponsList()
    {
        return Coupon::latest()->get();
    }
}
