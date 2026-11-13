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
                                            <span class="text-danger text-break">{{ $job->exception_summary }}</span>
                                        </td>
                                        <td><span class="text-nowrap">{{ $job->failed_at_display }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No failed jobs found.
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
@endsection
