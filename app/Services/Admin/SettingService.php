<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingService
{
    // ─────────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────────

    public function getGroup(string $group): array
    {
        return Setting::group($group);
    }

    // ─────────────────────────────────────────────
    // WRITE
    // ─────────────────────────────────────────────

    /**
     * @param  array  $data  Validated request data (may include UploadedFile instances)
     * @param  array  $fileKeys  Keys in $data that are file uploads
     * @param  array  $booleanKeys  Keys in $data that should be normalized to '1'/'0'
     */
    public function updateGroup(string $group, array $data, array $fileKeys = [], array $booleanKeys = []): array
    {
        return DB::transaction(function () use ($group, $data, $fileKeys, $booleanKeys) {

            foreach ($fileKeys as $fileKey) {
                if (! empty($data[$fileKey]) && $data[$fileKey] instanceof UploadedFile) {
                    $this->deleteFile(Setting::get($group, $fileKey));
                    $data[$fileKey] = $this->uploadFile($data[$fileKey], $group);
                } else {
                    unset($data[$fileKey]);
                }
            }

            foreach ($booleanKeys as $boolKey) {
                if (array_key_exists($boolKey, $data)) {
                    $data[$boolKey] = $this->resolveBoolean($data[$boolKey]) ? '1' : '0';
                }
            }

            $types = [];
            foreach ($fileKeys as $fileKey) {
                $types[$fileKey] = 'image';
            }
            foreach ($booleanKeys as $boolKey) {
                $types[$boolKey] = 'boolean';
            }

            // Any array value (e.g. a multi-select like operation_areas) is
            // stored as JSON automatically — no per-field handling needed
            // in the controller.
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = json_encode(array_values(array_filter($value, fn ($v) => trim((string) $v) !== '')));
                    $types[$key] = 'json';
                }
            }

            Setting::setMany($group, $data, $types);

            return Setting::group($group);
        });
    }

    public function removeFile(string $group, string $key): void
    {
        $this->deleteFile(Setting::get($group, $key));
        Setting::setMany($group, [$key => null], [$key => 'image']);
    }

    // ─────────────────────────────────────────────
    // FILE HANDLING
    // ─────────────────────────────────────────────

    private function uploadFile(UploadedFile $file, string $group): string
    {
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs("settings/{$group}", $filename, 'public');

        if (! $path) {
            throw new \Exception('Failed to upload file.');
        }

        return $path;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ─────────────────────────────────────────────
    // RESOLVE BOOLEAN
    // ─────────────────────────────────────────────

    private function resolveBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'on', 'yes', 'active']);
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // SHIPPING CHARGE RESOLUTION
    // The admin defines which district(s) their business operates in
    // (`operation_areas`). If the customer's shipping city/district
    // matches one of those areas, the lower "inside area" charge
    // applies; otherwise the higher "outside area" charge applies.
    // Nothing here is hardcoded to any specific city — it's entirely
    // driven by what the admin configures.
    // ─────────────────────────────────────────────

    public function getOperationAreas(): array
    {
        $raw = Setting::get('shipping', 'operation_areas', '[]');
        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function isWithinOperationArea(?string $city): bool
    {
        if (! $city) {
            return false;
        }

        $areas = $this->getOperationAreas();
        if (empty($areas)) {
            return false;
        }

        $city = strtolower(trim($city));

        foreach ($areas as $area) {
            $area = strtolower(trim((string) $area));
            if ($area === '') {
                continue;
            }

            // Matches "Dhaka" against "Uttara, Dhaka" as well as an exact match.
            if (str_contains($city, $area) || str_contains($area, $city)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolves the shipping charge for a given destination city and
     * order subtotal, applying the free-shipping threshold first.
     */
    public function resolveShippingCharge(?string $city, float $subtotal = 0.0): float
    {
        $settings = $this->getGroup('shipping');

        $freeShippingEnabled = ($settings['enable_free_shipping'] ?? '0') === '1';
        $freeShippingThreshold = (float) ($settings['free_shipping_threshold'] ?? 0);

        if ($freeShippingEnabled && $freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
            return 0.0;
        }

        $insideCharge = (float) ($settings['inside_area_charge'] ?? 0);
        $outsideCharge = (float) ($settings['outside_area_charge'] ?? 0);

        return $this->isWithinOperationArea($city) ? $insideCharge : $outsideCharge;
    }
}
