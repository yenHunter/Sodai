@extends('admin.include.vertical', ['title' => 'Queued Jobs'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'System', 'title' => 'Queued Jobs'])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Queued Jobs</h5>
            <span class="badge bg-primary-subtle text-primary">
                {{ number_format($jobs->total()) }} queued
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
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
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $job->queue }}</span>
                                </td>
                                <td><code class="text-break">{{ $job->job_class }}</code></td>
                                <td>{{ $job->attempts }}</td>
                                <td>
                                    <span
                                        class="badge {{ $job->status === 'Reserved' ? 'bg-warning' : ($job->status === 'Delayed' ? 'bg-info' : 'bg-success') }}">
                                        {{ $job->status }}
                                    </span>
                                </td>
                                <td><span class="text-nowrap">{{ $job->available_at_display }}</span></td>
                                <td><span class="text-nowrap">{{ $job->created_at_display }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No queued jobs found.</td>
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

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
