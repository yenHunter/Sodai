<?php

namespace App\Services\Visitor;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function store(User $customer, array $data): Address
    {
        return DB::transaction(function () use ($customer, $data) {
            $isDefault = (bool) ($data['is_default'] ?? false);

            if ($isDefault || !$customer->addresses()->exists()) {
                $customer->addresses()->update(['is_default' => false]);
                $isDefault = true;
            }

            return $customer->addresses()->create([
                ...$data,
                'is_default' => $isDefault,
            ]);
        });
    }

    public function update(Address $address, array $data): Address
    {
        return DB::transaction(function () use ($address, $data) {
            $isDefault = (bool) ($data['is_default'] ?? false);

            if ($isDefault) {
                Address::forUser($address->user_id)->update(['is_default' => false]);
            }

            $address->update([
                ...$data,
                'is_default' => $isDefault ?: $address->is_default,
            ]);

            return $address->fresh();
        });
    }

    public function delete(Address $address): bool
    {
        return DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $userId     = $address->user_id;

            $deleted = $address->delete();

            if ($wasDefault) {
                $next = Address::forUser($userId)->first();
                $next?->update(['is_default' => true]);
            }

            return $deleted;
        });
    }

    public function setDefault(Address $address): Address
    {
        return DB::transaction(function () use ($address) {
            Address::forUser($address->user_id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);

            return $address->fresh();
        });
    }

    public function getAddresses(User $customer)
    {
        return $customer->addresses()->orderByDesc('is_default')->latest()->get();
    }
}