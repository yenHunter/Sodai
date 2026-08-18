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
     * @param string $group
     * @param array  $data        Validated request data (may include UploadedFile instances)
     * @param array  $fileKeys    Keys in $data that are file uploads
     * @param array  $booleanKeys Keys in $data that should be normalized to '1'/'0'
     */
    public function updateGroup(string $group, array $data, array $fileKeys = [], array $booleanKeys = []): array
    {
        return DB::transaction(function () use ($group, $data, $fileKeys, $booleanKeys) {

            foreach ($fileKeys as $fileKey) {
                if (!empty($data[$fileKey]) && $data[$fileKey] instanceof UploadedFile) {
                    $this->deleteFile(Setting::get($group, $fileKey));
                    $data[$fileKey] = $this->uploadFile($data[$fileKey], $group);
                } else {
                    // No new file uploaded — never overwrite the existing path with null.
                    unset($data[$fileKey]);
                }
            }

            foreach ($booleanKeys as $boolKey) {
                if (array_key_exists($boolKey, $data)) {
                    $data[$boolKey] = $this->resolveBoolean($data[$boolKey]) ? '1' : '0';
                }
            }

            $types = [];
            foreach ($fileKeys as $fileKey)   $types[$fileKey] = 'image';
            foreach ($booleanKeys as $boolKey) $types[$boolKey] = 'boolean';

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
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs("settings/{$group}", $filename, 'public');

        if (!$path) {
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
        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1;
        if (is_string($value)) return in_array(strtolower($value), ['1', 'true', 'on', 'yes', 'active']);
        return false;
    }
}