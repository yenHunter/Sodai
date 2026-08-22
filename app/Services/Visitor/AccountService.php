<?php

namespace App\Services\Visitor;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountService
{
    public function updateProfile(User $customer, array $data): User
    {
        return DB::transaction(function () use ($customer, $data) {
            $avatarPath = $customer->avatar;

            if (! empty($data['avatar'])) {
                $this->deleteAvatar($customer->avatar);
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $customer->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'avatar' => $avatarPath,
            ]);

            return $customer->fresh();
        });
    }

    public function updatePassword(User $customer, string $newPassword): void
    {
        $customer->update(['password' => Hash::make($newPassword)]);
    }

    private function uploadAvatar(UploadedFile $image): string
    {
        $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('customers/avatars', $filename, 'public');

        if (! $path) {
            throw new \Exception('Failed to upload avatar.');
        }

        return $path;
    }

    private function deleteAvatar(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
