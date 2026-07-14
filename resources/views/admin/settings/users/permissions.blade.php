@extends('admin.include.vertical', ['title' => 'Permissions'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Permissions Reference'])

    <div class="card">
        <div class="card-body">
            <div class="row">
                @foreach ($groups as $group => $permissions)
                    <div class="col-md-4 mb-4">
                        <h6 class="text-uppercase text-muted fs-xs mb-2">{{ $group }}</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach ($permissions as $permission)
                                <li class="mb-1">
                                    <i class="text-success me-1" data-lucide="check" style="width:14px;height:14px"></i>
                                    {{ $permission->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection