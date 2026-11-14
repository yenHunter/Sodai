<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class LaravelLogReader
{
    public const LEVELS = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];

    private string $directory;

    private int $maxBytes;

    public function __construct(?string $directory = null, ?int $maxBytes = null)
    {
        $this->directory = rtrim(
            (string) ($directory ?? config('system_logs.directory', storage_path('logs'))),
            '/\\'
        );
        $this->maxBytes = max(1024, (int) ($maxBytes ?? config('system_logs.max_bytes', 2 * 1024 * 1024)));
    }

    /**
     * @return array<int, array{name: string, size: int, size_label: string, modified: int, modified_display: string}>
     */
    public function files(): array
    {
        if (! is_dir($this->directory)) {
            return [];
        }

        $files = [];

        foreach (glob($this->directory.'/*.log') ?: [] as $path) {
            if (is_link($path) || ! is_file($path)) {
                continue;
            }

            $size = filesize($path);
            $modified = filemtime($path);

            $files[] = [
                'name' => basename($path),
                'size' => $size === false ? 0 : $size,
                'size_label' => $this->formatBytes($size === false ? 0 : $size),
                'modified' => $modified === false ? 0 : $modified,
                'modified_display' => Carbon::createFromTimestamp($modified === false ? 0 : $modified)
                    ->format('d M Y, H:i'),
            ];
        }

        usort($files, fn (array $a, array $b) => $b['modified'] <=> $a['modified']);

        return $files;
    }

    /**
     * @param  array<int, array{name: string, size: int, size_label: string, modified: int, modified_display: string}>  $files
     * @return array{name: string, size: int, size_label: string, modified: int, modified_display: string}|null
     */
    public function resolveFile(string $requested, array $files): ?array
    {
        if ($files === []) {
            return null;
        }

        $name = basename(str_replace('\\', '/', $requested));

        foreach ($files as $file) {
            if ($file['name'] === $name) {
                return $file;
            }
        }

        return $files[0];
    }

    /**
     * @return array{entries: array<int, array<string, mixed>>, truncated: bool}
     */
    public function read(string $fileName): array
    {
        $path = $this->pathFor($fileName);

        if ($path === null) {
            return ['entries' => [], 'truncated' => false];
        }

        $size = filesize($path);
        $size = $size === false ? 0 : $size;
        $start = max(0, $size - $this->maxBytes);

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return ['entries' => [], 'truncated' => false];
        }

        if ($start > 0) {
            fseek($handle, $start);
            fgets($handle);
        }

        $content = stream_get_contents($handle);
        fclose($handle);

        return [
            'entries' => $this->parse($content === false ? '' : $content),
            'truncated' => $start > 0,
        ];
    }

    private function pathFor(string $fileName): ?string
    {
        $name = basename(str_replace('\\', '/', $fileName));

        if ($name === '' || ! str_ends_with(strtolower($name), '.log')) {
            return null;
        }

        $path = $this->directory.DIRECTORY_SEPARATOR.$name;

        if (is_link($path) || ! is_file($path)) {
            return null;
        }

        return $path;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parse(string $content): array
    {
        $lines = preg_split('/\r\n|\n|\r/', $content) ?: [];
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match(
                '/^\[(?<timestamp>[^\]]+)\]\s+(?<environment>[^\.\s]+)\.(?<level>[A-Z]+):\s?(?<message>.*)$/',
                $line,
                $matches
            ) === 1) {
                if ($current !== null) {
                    $entries[] = $this->finalize($current);
                }

                $current = [
                    'timestamp' => $matches['timestamp'],
                    'environment' => $matches['environment'],
                    'level' => strtolower($matches['level']),
                    'message' => $matches['message'],
                    'raw' => [$line],
                ];

                continue;
            }

            if ($current !== null) {
                $current['raw'][] = $line;
            }
        }

        if ($current !== null) {
            $entries[] = $this->finalize($current);
        }

        return $entries;
    }

    /**
     * @param  array{timestamp: string, environment: string, level: string, message: string, raw: array<int, string>}  $entry
     * @return array<string, mixed>
     */
    private function finalize(array $entry): array
    {
        $message = trim($entry['message']);
        $jsonStart = strpos($message, ' {"');

        // Monolog appends its context object after the human-readable message.
        // Keep that context in the expanded raw entry, but out of the summary.
        if ($jsonStart !== false) {
            $message = rtrim(substr($message, 0, $jsonStart));
        }

        if ($message === '') {
            $message = '(no message)';
        }

        $timestamp = $entry['timestamp'];

        try {
            $timestamp = Carbon::parse($timestamp)->format('d M Y, H:i:s');
        } catch (Throwable) {
            // Keep the original timestamp when it is not a parseable date.
        }

        $raw = implode("\n", $entry['raw']);
        $detailsTruncated = strlen($raw) > 30000;

        if ($detailsTruncated) {
            $raw = substr($raw, 0, 30000)."\n\n... [entry truncated]";
        }

        return [
            'timestamp' => $timestamp,
            'environment' => $entry['environment'],
            'level' => $entry['level'],
            'level_class' => $this->levelClass($entry['level']),
            'message' => $message,
            'summary' => Str::limit($message, 220),
            'raw' => $raw,
            'details_truncated' => $detailsTruncated,
        ];
    }

    private function levelClass(string $level): string
    {
        return match ($level) {
            'error', 'critical', 'alert', 'emergency' => 'danger',
            'warning' => 'warning',
            'notice', 'info' => 'info',
            default => 'secondary',
        };
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = max(0, $bytes);
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return ($unit === 0 ? (string) $size : number_format($size, 1)).' '.$units[$unit];
    }
}
