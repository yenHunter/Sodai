@extends("shared.base", ["title" => "Embed Videos"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "UI", "title" => "Videos"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 21:9</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-21x9">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 1:1</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-1x1">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 16:9</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 4:3</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-4x3">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include("shared.partials.footer")
        </div>
    </div>

    @include("shared.partials.customizer") @include("shared.partials.footer-scripts")
@endsection

@section("scripts")
@endsection
