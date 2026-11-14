<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\LaravelLogReader;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SystemLogController extends Controller
{
    public function index(Request $request, LaravelLogReader $logs)
    {
        $this->ensureSuperAdmin();

        $files = $logs->files();
        $requestedFile = $this->queryString($request, 'file', 255);
        $selectedFile = $logs->resolveFile($requestedFile, $files);

        $level = strtolower($this->queryString($request, 'level', 20));

        if (! in_array($level, LaravelLogReader::LEVELS, true)) {
            $level = '';
        }

        $search = $this->queryString($request, 'q', 200);
        $result = $selectedFile === null
            ? ['entries' => [], 'truncated' => false]
            : $logs->read($selectedFile['name']);

        $entries = collect($result['entries'])
            ->when($level !== '', fn ($collection) => $collection->where('level', $level))
            ->when($search !== '', function ($collection) use ($search) {
                $needle = Str::lower($search);

                return $collection->filter(
                    fn (array $entry) => Str::contains(Str::lower($entry['raw']), $needle)
                );
            })
            ->reverse()
            ->values();

        $perPage = max(1, (int) config('system_logs.per_page', 25));
        $page = max(1, (int) $request->query('logs_page', 1));

        $entries = new LengthAwarePaginator(
            $entries->forPage($page, $perPage)->values()->all(),
            $entries->count(),
            $perPage,
            $page,
            [
                'path' => route('admin.system.logs.index'),
                'query' => array_filter([
                    'file' => $selectedFile['name'] ?? '',
                    'level' => $level,
                    'q' => $search,
                ], fn ($value) => $value !== ''),
            ]
        );

        return view('admin.system.logs.index', [
            'files' => $files,
            'selectedFile' => $selectedFile,
            'level' => $level,
            'search' => $search,
            'entries' => $entries,
            'truncated' => $result['truncated'],
            'levels' => LaravelLogReader::LEVELS,
        ]);
    }

    private function queryString(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
    }
}
