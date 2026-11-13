<ul class="nav nav-tabs mb-3" id="systemJobsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link @if ($activeTab === 'queued') active @endif" id="queued-jobs-tab"
            data-bs-toggle="tab" href="#queued-jobs-pane" role="tab" aria-controls="queued-jobs-pane"
            aria-selected="{{ $activeTab === 'queued' ? 'true' : 'false' }}">
            Queued Jobs
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link @if ($activeTab === 'failed') active @endif" id="failed-jobs-tab"
            data-bs-toggle="tab" href="#failed-jobs-pane" role="tab" aria-controls="failed-jobs-pane"
            aria-selected="{{ $activeTab === 'failed' ? 'true' : 'false' }}">
            Failed Jobs
        </a>
    </li>
</ul>
