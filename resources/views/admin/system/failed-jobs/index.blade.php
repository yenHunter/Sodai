@extends('admin.include.vertical', ['title' => 'Failed Jobs'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'System', 'title' => 'Failed Jobs'])

    @include('admin.system.partials.tabs')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="card-title mb-0">Failed Jobs</h5>
                    <span class="badge bg-danger-subtle text-danger">
                        {{ number_format($failedJobs->total()) }} jobs
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.system.failed-jobs.index') }}" method="GET"
                        class="row g-2 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="queueFilter">Queue</label>
                            <select class="form-select" id="queueFilter" name="queue">
                                <option value="">All Queues</option>
                                @foreach ($queues as $queueOption)
                                    <option value="{{ $queueOption }}" @selected($queue === $queueOption)>
                                        {{ $queueOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold" for="jobClassFilter">Job Class</label>
                            <div class="app-search">
                                <input type="search" class="form-control" id="jobClassFilter" name="job_class"
                                    value="{{ $jobClass }}" placeholder="Filter by job class...">
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fs-sm me-1" data-lucide="filter"></i> Filter
                            </button>
                            @if ($queue !== '' || $jobClass !== '')
                                <a href="{{ route('admin.system.failed-jobs.index') }}" class="btn btn-light">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped dt-responsive align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="70">ID</th>
                                    <th width="130">Queue</th>
                                    <th>Job</th>
                                    <th>Exception</th>
                                    <th width="150">Failed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($failedJobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary">{{ $job->queue }}</span>
                                        </td>
                                        <td><code class="text-break">{{ $job->job_class }}</code></td>
                                        <td>
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <span class="text-danger text-break">{{ $job->exception_summary }}</span>
                                                <button class="btn btn-sm btn-link p-0 text-nowrap" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#failure-details-{{ $job->id }}"
                                                    aria-expanded="false"
                                                    aria-controls="failure-details-{{ $job->id }}">
                                                    View details
                                                    <i class="fs-sm ms-1" data-lucide="chevron-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><span class="text-nowrap">{{ $job->failed_at_display }}</span></td>
                                    </tr>
                                    <tr class="collapse" id="failure-details-{{ $job->id }}">
                                        <td colspan="5" class="bg-light">
                                            <div class="p-2">
                                                <div
                                                    class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                                    <h6 class="mb-0">Failure details</h6>
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span class="text-muted fs-12 text-break">
                                                            UUID: <code>{{ $job->uuid }}</code>
                                                        </span>
                                                        <button class="btn btn-sm btn-light" type="button"
                                                            data-copy-failure
                                                            data-copy-target="failure-exception-{{ $job->id }}">
                                                            <i class="fs-sm me-1" data-lucide="copy"></i>
                                                            <span data-copy-label>Copy exception</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <pre class="mb-0 text-danger text-wrap text-break"
                                                    id="failure-exception-{{ $job->id }}">{{ $job->exception }}</pre>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            @if ($queue !== '' || $jobClass !== '')
                                                No failed jobs match these filters.
                                            @else
                                                No failed jobs found.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($failedJobs->hasPages())
                    <div class="card-footer border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $failedJobs->firstItem() }}–{{ $failedJobs->lastItem() }} of
                                {{ $failedJobs->total() }}
                            </div>
                            {{ $failedJobs->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-copy-failure]').forEach(function(button) {
                button.addEventListener('click', async function() {
                    const target = document.getElementById(button.dataset.copyTarget)
                    const label = button.querySelector('[data-copy-label]')

                    if (!target) {
                        return
                    }

                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(target.textContent)
                        } else {
                            const textarea = document.createElement('textarea')
                            textarea.value = target.textContent
                            textarea.setAttribute('readonly', '')
                            textarea.style.position = 'fixed'
                            textarea.style.opacity = '0'
                            document.body.appendChild(textarea)
                            textarea.select()

                            if (!document.execCommand('copy')) {
                                throw new Error('Copy command failed')
                            }

                            textarea.remove()
                        }

                        label.textContent = 'Copied'
                    } catch (error) {
                        label.textContent = 'Copy failed'
                    }

                    setTimeout(function() {
                        label.textContent = 'Copy exception'
                    }, 2000)
                })
            })
        })
    </script>
@endsection
