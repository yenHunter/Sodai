@extends('admin.include.vertical', ['title' => 'Laravel System Logs'])

@section('styles')
    <style>
        .system-logs-table {
            min-width: 900px;
        }

        .system-logs-table .log-message {
            min-width: 320px;
        }

        .log-entry-details {
            max-height: 28rem;
            overflow: auto;
            white-space: pre-wrap;
        }
    </style>
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'System', 'title' => 'Laravel System Logs'])

    <div class="card">
        <div class="card-header justify-content-between">
            <h5 class="card-title mb-0">Laravel System Logs</h5>
            @if ($selectedFile !== null)
                <span class="badge bg-primary-subtle text-primary">
                    {{ number_format($entries->total()) }} {{ $entries->total() === 1 ? 'entry' : 'entries' }}
                </span>
            @endif
        </div>

        <div class="card-body">
            @if ($files === [])
                <div class="text-center text-muted py-5">
                    <i class="fs-1" data-lucide="file-text"></i>
                    <p class="mb-1 mt-3 fw-semibold">No Laravel log files found.</p>
                    <p class="mb-0 fs-sm">Log files will appear here after Laravel writes to
                        <code>storage/logs</code>.
                    </p>
                </div>
            @else
                <form action="{{ route('admin.system.logs.index') }}" method="GET"
                    class="row g-2 align-items-end mb-3">
                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold" for="logFileFilter">Log File</label>
                        <select class="form-select" id="logFileFilter" name="file">
                            @foreach ($files as $file)
                                <option value="{{ $file['name'] }}" @selected($selectedFile['name'] === $file['name'])>
                                    {{ $file['name'] }} ({{ $file['size_label'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <label class="form-label fw-semibold" for="logLevelFilter">Level</label>
                        <select class="form-select" id="logLevelFilter" name="level">
                            <option value="">All Levels</option>
                            @foreach ($levels as $levelOption)
                                <option value="{{ $levelOption }}" @selected($level === $levelOption)>
                                    {{ strtoupper($levelOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold" for="logSearchFilter">Search</label>
                        <div class="app-search">
                            <input class="form-control" id="logSearchFilter" name="q" type="search"
                                value="{{ $search }}" placeholder="Search log entries...">
                            <i class="app-search-icon text-muted" data-lucide="search"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-6 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fs-sm me-1" data-lucide="filter"></i> Filter
                        </button>
                        @if ($level !== '' || $search !== '' || request()->filled('file'))
                            <a class="btn btn-light" href="{{ route('admin.system.logs.index') }}">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="text-muted fs-sm">
                        <i class="fs-sm me-1" data-lucide="file-clock"></i>
                        {{ $selectedFile['name'] }} · {{ $selectedFile['size_label'] }} · updated
                        {{ $selectedFile['modified_display'] }}
                    </span>
                </div>

                @if ($truncated)
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="fs-sm me-1" data-lucide="triangle-alert"></i>
                        Only the newest portion of this log file is loaded. Older entries are not shown.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped dt-responsive align-middle mb-0 system-logs-table">
                        <thead>
                            <tr>
                                <th width="110">Level</th>
                                <th width="175">Timestamp</th>
                                <th width="115">Environment</th>
                                <th>Message</th>
                                <th width="110">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entries as $entry)
                                <tr>
                                    <td>
                                        <span
                                            class="badge bg-{{ $entry['level_class'] }}-subtle text-{{ $entry['level_class'] }}">
                                            {{ strtoupper($entry['level']) }}
                                        </span>
                                    </td>
                                    <td><span class="text-nowrap">{{ $entry['timestamp'] }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $entry['environment'] }}</span>
                                    </td>
                                    <td class="log-message">
                                        <span class="text-break">{{ $entry['summary'] }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-link p-0 text-nowrap" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#log-details-{{ $loop->index }}"
                                            aria-expanded="false"
                                            aria-controls="log-details-{{ $loop->index }}">
                                            View details
                                            <i class="fs-sm ms-1" data-lucide="chevron-down"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse" id="log-details-{{ $loop->index }}">
                                    <td class="bg-light" colspan="5">
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Log entry</h6>
                                                @if ($entry['details_truncated'])
                                                    <span class="badge bg-warning-subtle text-warning">Truncated</span>
                                                @endif
                                            </div>
                                            <pre class="log-entry-details mb-0 text-body">{{ $entry['raw'] }}</pre>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-muted py-4" colspan="5">
                                        @if ($level !== '' || $search !== '')
                                            No log entries match these filters.
                                        @else
                                            No log entries found in this file.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($entries->hasPages())
            <div class="card-footer border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of
                        {{ $entries->total() }}
                    </div>
                    {{ $entries->links() }}
                </div>
            </div>
        @endif
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection
