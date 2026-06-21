@extends("shared.base", ["title" => "Popovers"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "UI", "title" => "Popovers"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Simple Popover</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-info" data-bs-content="Click here to get support from our team. We're here 24/7 to assist you." data-bs-toggle="popover" title="Need Help?" type="button">Get Support Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dismiss on Next Click</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary" data-bs-content="Get quick tips and tricks to improve your workflow instantly." data-bs-toggle="popover" data-bs-trigger="focus" tabindex="0" title="Quick Tips" type="button">Show Tips</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Hover</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-dark" data-bs-content="Discover features you didnâ€™t know existed. Hover to explore more!" data-bs-toggle="popover" data-bs-trigger="hover" tabindex="0" title="Exciting Features!" type="button">Please Hover Me</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Four Directions</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-primary" data-bs-content="This popover appears above the button. Great for tips or info." data-bs-placement="top" data-bs-toggle="popover" title="Top Popover" type="button">Popover on top</button>
                                    <button class="btn btn-primary" data-bs-content="This popover shows below. Perfect for additional details." data-bs-placement="bottom" data-bs-toggle="popover" title="Bottom Popover" type="button">Popover on bottom</button>
                                    <button class="btn btn-primary" data-bs-content="Slide in from the right to provide quick insights." data-bs-placement="right" data-bs-toggle="popover" title="Right Popover" type="button">Popover on right</button>
                                    <button class="btn btn-primary" data-bs-content="Appears on the left side. Great for tooltips or notes." data-bs-placement="left" data-bs-toggle="popover" title="Left Popover" type="button">Popover on left</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Custom Popovers</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-primary" data-bs-content="This is a primary-themed popover styled using CSS variables." data-bs-custom-class="popover-primary" data-bs-placement="right" data-bs-title="Primary Popover" data-bs-toggle="popover"
                                        type="button">
                                        Primary Popover
                                    </button>
                                    <button class="btn btn-success" data-bs-content="This is a success-themed popover styled using CSS variables." data-bs-custom-class="popover-success" data-bs-placement="right" data-bs-title="Success Popover" data-bs-toggle="popover"
                                        type="button">
                                        Success Popover
                                    </button>
                                    <button class="btn btn-danger" data-bs-content="This is a danger-themed popover styled using CSS variables." data-bs-custom-class="popover-danger" data-bs-placement="right" data-bs-title="Danger Popover" data-bs-toggle="popover"
                                        type="button">
                                        Danger Popover
                                    </button>
                                    <button class="btn btn-info" data-bs-content="This is an info-themed popover styled using CSS variables." data-bs-custom-class="popover-info" data-bs-placement="right" data-bs-title="Info Popover" data-bs-toggle="popover" type="button">
                                        Info Popover
                                    </button>
                                    <button class="btn btn-dark" data-bs-content="This is a dark-themed popover styled using CSS variables." data-bs-custom-class="popover-dark" data-bs-placement="right" data-bs-title="Dark Popover" data-bs-toggle="popover" type="button">
                                        Dark Popover
                                    </button>
                                    <button class="btn btn-secondary" data-bs-content="This is a secondary-themed popover styled using CSS variables." data-bs-custom-class="popover-secondary" data-bs-placement="right" data-bs-title="Secondary Popover" data-bs-toggle="popover"
                                        type="button">
                                        Secondary Popover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Disabled Elements</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="d-inline-block" data-bs-content="This button is disabled, but the popover still works." data-bs-placement="top" data-bs-toggle="popover">
                                    <button class="btn btn-primary" disabled="" style="pointer-events: none" type="button">Disabled Button</button>
                                </span>
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
