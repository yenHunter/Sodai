<?php

namespace Tests\Unit;

use App\Rules\SafeUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SafeUrlTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function acceptedUrls(): array
    {
        return [
            'app relative path' => ['/products'],
            'nested path with query' => ['/products?sort=price_asc'],
            'https absolute url' => ['https://external.example.com/sale'],
            'http absolute url' => ['http://example.com'],
            'url with port' => ['https://example.com:8443/path'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rejectedUrls(): array
    {
        return [
            'javascript scheme' => ['javascript:alert(1)'],
            'mixed case javascript' => ['JaVaScRiPt:alert(1)'],
            'data scheme' => ['data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='],
            'vbscript scheme' => ['vbscript:msgbox(1)'],
            'protocol relative' => ['//evil.example.com'],
            'scheme relative without host' => ['https://'],
            'relative without slash' => ['products'],
            'newline smuggling' => ["java\nscript:alert(1)"],
            'whitespace in url' => ['https://example.com /evil'],
        ];
    }

    #[DataProvider('acceptedUrls')]
    public function test_it_accepts_relative_and_http_urls(string $url): void
    {
        $this->assertSame([], $this->failuresFor($url));
    }

    #[DataProvider('rejectedUrls')]
    public function test_it_rejects_unsafe_link_targets(string $url): void
    {
        $this->assertNotSame([], $this->failuresFor($url));
    }

    public function test_it_allows_empty_and_null_values(): void
    {
        $this->assertSame([], $this->failuresFor(''));
        $this->assertSame([], $this->failuresFor(null));
    }

    /**
     * @return array<int, string>
     */
    private function failuresFor(mixed $value): array
    {
        $failures = [];

        (new SafeUrl)->validate('button_url', $value, function (string $message) use (&$failures): void {
            $failures[] = $message;
        });

        return $failures;
    }
}
