@extends('admin.include.vertical', ['title' => 'CMS Pages'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'CMS', 'title' => 'Static Pages'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @foreach ($pages as $page)
            <div class="col-md-6 col-xxl-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar avatar-xxl flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-32">
                                    <i data-lucide="file-text"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $page->title }}</h5>
                                <small class="text-muted">/{{ $page->slug }}</small>
                            </div>
                        </div>

                        <p class="text-muted small mb-3">
                            @if ($page->updated_at)
                                Last updated {{ $page->updated_at->diffForHumans() }}
                                @if ($page->updatedBy)
                                    by {{ $page->updatedBy->name }}
                                @endif
                            @else
                                Not yet edited.
                            @endif
                        </p>

                        @if (blank($page->content))
                            <span class="badge badge-soft-warning fs-xxs mb-3 align-self-start">Empty</span>
                        @else
                            <span class="badge badge-soft-success fs-xxs mb-3 align-self-start">Published</span>
                        @endif

                        <a href="{{ route('admin.cms.pages.edit', $page->slug) }}" class="btn btn-primary btn-sm mt-auto">
                            <i data-lucide="square-pen" class="fs-sm me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
