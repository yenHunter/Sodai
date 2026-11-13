<ul class="nav nav-tabs nav-bordered mb-3">
    <li class="nav-item">
        <a class="nav-link @if (request()->routeIs('admin.system.queued-jobs.*')) active @endif"
            href="{{ route('admin.system.queued-jobs.index') }}"
            @if (request()->routeIs('admin.system.queued-jobs.*')) aria-current="page" @endif>
            <i class="fs-sm me-1" data-lucide="list-checks"></i> Queued Jobs
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if (request()->routeIs('admin.system.failed-jobs.*')) active @endif"
            href="{{ route('admin.system.failed-jobs.index') }}"
            @if (request()->routeIs('admin.system.failed-jobs.*')) aria-current="page" @endif>
            <i class="fs-sm me-1" data-lucide="circle-alert"></i> Failed Jobs
        </a>
    </li>
</ul>
