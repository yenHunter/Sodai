@extends('admin.include.vertical', ['title' => 'Queued Jobs'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'System', 'title' => 'Queued Jobs'])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="card-title mb-0">Queued Jobs</h5>
                    <span class="badge bg-primary-subtle text-primary">
                        {{ number_format($jobs->total()) }} jobs
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.system.queued-jobs.index') }}" method="GET"
                        class="row g-2 align-items-end mb-3">
                        <div class="col-lg-3 col-md-6">
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
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold" for="statusFilter">Status</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" @selected($status === 'pending')>Pending</option>
                                <option value="delayed" @selected($status === 'delayed')>Delayed</option>
                                <option value="reserved" @selected($status === 'reserved')>Reserved</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold" for="jobClassFilter">Job Class</label>
                            <div class="app-search">
                                <input type="search" class="form-control" id="jobClassFilter" name="job_class"
                                    value="{{ $jobClass }}" placeholder="Filter by job class...">
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fs-sm me-1" data-lucide="filter"></i> Filter
                            </button>
                            @if ($queue !== '' || $jobClass !== '' || $status !== '')
                                <a href="{{ route('admin.system.queued-jobs.index') }}" class="btn btn-light">
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
                                    <th width="90">Attempts</th>
                                    <th width="110">Status</th>
                                    <th width="150">Available</th>
                                    <th width="150">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary">{{ $job->queue }}</span>
                                        </td>
                                        <td><code class="text-break">{{ $job->job_class }}</code></td>
                                        <td>{{ $job->attempts }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $job->status === 'Reserved' ? 'bg-warning-subtle text-warning' : ($job->status === 'Delayed' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success') }}">
                                                {{ $job->status }}
                                            </span>
                                        </td>
                                        <td><span class="text-nowrap">{{ $job->available_at_display }}</span></td>
                                        <td><span class="text-nowrap">{{ $job->created_at_display }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            @if ($queue !== '' || $jobClass !== '' || $status !== '')
                                                No queued jobs match these filters.
                                            @else
                                                No queued jobs found.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($jobs->hasPages())
                    <div class="card-footer border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }}
                            </div>
                            {{ $jobs->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
