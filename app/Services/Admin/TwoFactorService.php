<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Str;

/**
 * Minimal RFC 6238 TOTP implementation (SHA-1, 6 digits, 30-second period)
 * so admin MFA does not require an extra package. Secrets are base32 and are
 * stored encrypted by the Admin model cast.
 */
class TwoFactorService
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const PERIOD = 30;

    private const DIGITS = 6;

    public function generateSecret(int $length = 32): string
    {
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $secret;
    }

    public function otpauthUrl(Admin $admin, string $secret): string
    {
        $issuer = rawurlencode((string) config('app.name', 'Sodai'));
        $label = rawurlencode($issuer.':'.$admin->email);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits="
            .self::DIGITS.'&period='.self::PERIOD;
    }

    /**
     * Current code for a secret. Exposed for the setup screen/tests.
     */
    public function currentCode(string $secret, ?int $timestamp = null): string
    {
        return $this->hotp($secret, intdiv($timestamp ?? time(), self::PERIOD));
    }

    /**
     * Verify a code allowing one period of clock drift in either direction.
     */
    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if (preg_match('/^\d{'.self::DIGITS.'}$/', $code) !== 1) {
            return false;
        }

        $counter = intdiv(time(), self::PERIOD);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->hotp($secret, $counter + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(5)).'-'.strtoupper(Str::random(5));
        }

        return $codes;
    }

    private function hotp(string $secret, int $counter): string
    {
        // 8-byte big-endian counter, then HMAC-SHA1 + dynamic truncation.
        $binaryCounter = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $this->base32Decode($secret), true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $secret) ?? '');

        $bits = '';
        foreach (str_split($secret) as $character) {
            $bits .= str_pad(decbin((int) strpos(self::ALPHABET, $character)), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr((int) bindec($byte));
            }
        }

        return $binary;
    }
}
