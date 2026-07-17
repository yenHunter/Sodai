<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Admin\CustomerSetPasswordMail;

class CustomerService
{
    private const TOKEN_EXPIRY_MINUTES = 60;

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $avatarPath = null;
            if (!empty($data['avatar'])) {
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $customer = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'avatar'   => $avatarPath,
                // Temporary, unusable password — customer sets a real one via emailed link.
                'password' => Hash::make(Str::random(40)),
                'status'   => $data['status'],
            ]);

            $this->sendSetPasswordEmail($customer);

            Log::info('Customer created by admin.', ['customer_id' => $customer->id]);

            return $customer->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(User $customer, array $data): User
    {
        return DB::transaction(function () use ($customer, $data) {

            $avatarPath = $customer->avatar;

            if (!empty($data['avatar'])) {
                $this->deleteAvatar($customer->avatar);
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $customer->update([
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['phone'] ?? null,
                'avatar' => $avatarPath,
                'status' => $data['status'],
            ]);

            return $customer->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(User $customer): bool
    {
        if ($customer->orders()->exists()) {
            throw new \Exception('Cannot delete a customer who has existing orders.');
        }

        return DB::transaction(function () use ($customer) {
            $this->deleteAvatar($customer->avatar);
            return $customer->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS (active / inactive only — banning is a separate action)
    // ─────────────────────────────────────────────

    public function toggleStatus(User $customer): User
    {
        if ($customer->status === 'banned') {
            throw new \Exception('This customer is banned. Unban them first from their profile.');
        }

        $customer->update([
            'status' => $customer->status === 'active' ? 'inactive' : 'active',
        ]);

        return $customer->fresh();
    }

    // ─────────────────────────────────────────────
    // SET-PASSWORD EMAIL (reused for resend)
    // ─────────────────────────────────────────────

    public function sendSetPasswordEmail(User $customer): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $customer->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $customer->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $setPasswordUrl = route('customer.set-password.view', [
            'token' => $token,
            'email' => $customer->email,
        ]);

        Mail::to($customer->email)->send(new CustomerSetPasswordMail(
            setPasswordUrl:   $setPasswordUrl,
            customerName:     $customer->name,
            expiresInMinutes: self::TOKEN_EXPIRY_MINUTES,
        ));
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadAvatar(UploadedFile $image): string
    {
        try {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path     = $image->storeAs('customers/avatars', $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to upload avatar.');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Avatar upload failed: ' . $e->getMessage());
        }
    }

    private function deleteAvatar(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getCustomersList()
    {
        return User::withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->with('defaultAddress')
            ->latest()
            ->get();
    }
}