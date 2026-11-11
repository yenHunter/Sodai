import { createHmac } from 'node:crypto';

const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
const PERIOD_SECONDS = 30;
const DIGITS = 6;

/**
 * Current TOTP code for a base32 secret (SHA-1, 6 digits, 30 s), matching
 * App\Services\Admin\TwoFactorService.
 */
export function generateTotp(secret, timestamp = Date.now()) {
    const counter = Math.floor(timestamp / 1000 / PERIOD_SECONDS);

    const message = Buffer.alloc(8);
    message.writeBigUInt64BE(BigInt(counter));

    const digest = createHmac('sha1', base32Decode(secret)).update(message).digest();
    const offset = digest[digest.length - 1] & 0x0f;
    const binary = digest.readUInt32BE(offset) & 0x7fffffff;

    return String(binary % 10 ** DIGITS).padStart(DIGITS, '0');
}

function base32Decode(secret) {
    const clean = secret.toUpperCase().replace(/[^A-Z2-7]/g, '');

    let bits = '';
    for (const character of clean) {
        bits += ALPHABET.indexOf(character).toString(2).padStart(5, '0');
    }

    const bytes = [];
    for (let index = 0; index + 8 <= bits.length; index += 8) {
        bytes.push(Number.parseInt(bits.slice(index, index + 8), 2));
    }

    return Buffer.from(bytes);
}
