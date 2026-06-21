@extends("shared.base", ["title" => "Buttons"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "UI", "title" => "Buttons"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Default Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use any of the available
                                    <code>&lt;a&gt;</code>
                                    ,
                                    <code>&lt;button&gt;</code>
                                    , or
                                    <code>&lt;input&gt;</code>
                                    classes
                                    <code>.btn</code>
                                    to quickly create a styled button.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-default" type="button">Default</button>
                                    <button class="btn btn-primary" type="button">Primary</button>
                                    <button class="btn btn-secondary" type="button">Secondary</button>
                                    <button class="btn btn-success" type="button">Success</button>
                                    <button class="btn btn-danger" type="button">Danger</button>
                                    <button class="btn btn-warning" type="button">Warning</button>
                                    <button class="btn btn-info" type="button">Info</button>
                                    <button class="btn btn-light" type="button">Light</button>
                                    <button class="btn btn-dark" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Rounded</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>.rounded-pill</code>
                                    with a default button to give it pill-shaped rounded corners.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-default rounded-pill" type="button">Default</button>
                                    <button class="btn btn-primary rounded-pill" type="button">Primary</button>
                                    <button class="btn btn-secondary rounded-pill" type="button">Secondary</button>
                                    <button class="btn btn-success rounded-pill" type="button">Success</button>
                                    <button class="btn btn-danger rounded-pill" type="button">Danger</button>
                                    <button class="btn btn-warning rounded-pill" type="button">Warning</button>
                                    <button class="btn btn-info rounded-pill" type="button">Info</button>
                                    <button class="btn btn-light rounded-pill" type="button">Light</button>
                                    <button class="btn btn-dark rounded-pill" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Outline</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>.btn-outline-**</code>
                                    classes to quickly create buttons with borders.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-outline-primary" type="button">Primary</button>
                                    <button class="btn btn-outline-secondary" type="button">Secondary</button>
                                    <button class="btn btn-outline-success" type="button">Success</button>
                                    <button class="btn btn-outline-danger" type="button">Danger</button>
                                    <button class="btn btn-outline-warning" type="button">Warning</button>
                                    <button class="btn btn-outline-info" type="button">Info</button>
                                    <button class="btn btn-outline-light" type="button">Light</button>
                                    <button class="btn btn-outline-dark" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Outline Rounded</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>.rounded-pill</code>
                                    with an outline button to give it pill-shaped rounded corners.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-outline-primary rounded-pill" type="button">Primary</button>
                                    <button class="btn btn-outline-secondary rounded-pill" type="button">Secondary</button>
                                    <button class="btn btn-outline-success rounded-pill" type="button">Success</button>
                                    <button class="btn btn-outline-danger rounded-pill" type="button">Danger</button>
                                    <button class="btn btn-outline-warning rounded-pill" type="button">Warning</button>
                                    <button class="btn btn-outline-info rounded-pill" type="button">Info</button>
                                    <button class="btn btn-outline-light rounded-pill" type="button">Light</button>
                                    <button class="btn btn-outline-dark rounded-pill" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Soft Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>btn-soft-**</code>
                                    class with the below-mentioned variation to create a button with the soft background.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-soft-primary" type="button">Primary</button>
                                    <button class="btn btn-soft-secondary" type="button">Secondary</button>
                                    <button class="btn btn-soft-success" type="button">Success</button>
                                    <button class="btn btn-soft-danger" type="button">Danger</button>
                                    <button class="btn btn-soft-warning" type="button">Warning</button>
                                    <button class="btn btn-soft-info" type="button">Info</button>
                                    <button class="btn btn-soft-dark" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Soft Rounded Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>btn-soft-**</code>
                                    class along with
                                    <code>.rounded-pill</code>
                                    to create a softly styled button with rounded corners.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-soft-primary rounded-pill" type="button">Primary</button>
                                    <button class="btn btn-soft-secondary rounded-pill" type="button">Secondary</button>
                                    <button class="btn btn-soft-success rounded-pill" type="button">Success</button>
                                    <button class="btn btn-soft-danger rounded-pill" type="button">Danger</button>
                                    <button class="btn btn-soft-warning rounded-pill" type="button">Warning</button>
                                    <button class="btn btn-soft-info rounded-pill" type="button">Info</button>
                                    <button class="btn btn-soft-dark rounded-pill" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Ghost Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>btn-ghost-**</code>
                                    class to create buttons with a transparent background that highlight with color on hover.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-ghost-primary" type="button">Primary</button>
                                    <button class="btn btn-ghost-secondary" type="button">Secondary</button>
                                    <button class="btn btn-ghost-success" type="button">Success</button>
                                    <button class="btn btn-ghost-danger" type="button">Danger</button>
                                    <button class="btn btn-ghost-warning" type="button">Warning</button>
                                    <button class="btn btn-ghost-info" type="button">Info</button>
                                    <button class="btn btn-ghost-dark" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Ghost Rounded Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>btn-ghost-**</code>
                                    with
                                    <code>.rounded-pill</code>
                                    for rounded ghost buttons that highlight on hover.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-ghost-primary rounded-pill" type="button">Primary</button>
                                    <button class="btn btn-ghost-secondary rounded-pill" type="button">Secondary</button>
                                    <button class="btn btn-ghost-success rounded-pill" type="button">Success</button>
                                    <button class="btn btn-ghost-danger rounded-pill" type="button">Danger</button>
                                    <button class="btn btn-ghost-warning rounded-pill" type="button">Warning</button>
                                    <button class="btn btn-ghost-info rounded-pill" type="button">Info</button>
                                    <button class="btn btn-ghost-dark rounded-pill" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Gradient Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>.bg-gradient</code>
                                    class to apply a gradient style to buttons.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-default bg-gradient" type="button">Default</button>
                                    <button class="btn btn-primary bg-gradient" type="button">Primary</button>
                                    <button class="btn btn-secondary bg-gradient" type="button">Secondary</button>
                                    <button class="btn btn-success bg-gradient" type="button">Success</button>
                                    <button class="btn btn-danger bg-gradient" type="button">Danger</button>
                                    <button class="btn btn-warning bg-gradient" type="button">Warning</button>
                                    <button class="btn btn-info bg-gradient" type="button">Info</button>
                                    <button class="btn btn-light bg-gradient" type="button">Light</button>
                                    <button class="btn btn-dark bg-gradient" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Gradient Rounded Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>.bg-gradient</code>
                                    and
                                    <code>.rounded-pill</code>
                                    classes to apply a gradient style with rounded edges to buttons.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-default rounded-pill bg-gradient" type="button">Default</button>
                                    <button class="btn btn-primary rounded-pill bg-gradient" type="button">Primary</button>
                                    <button class="btn btn-secondary rounded-pill bg-gradient" type="button">Secondary</button>
                                    <button class="btn btn-success rounded-pill bg-gradient" type="button">Success</button>
                                    <button class="btn btn-danger rounded-pill bg-gradient" type="button">Danger</button>
                                    <button class="btn btn-warning rounded-pill bg-gradient" type="button">Warning</button>
                                    <button class="btn btn-info rounded-pill bg-gradient" type="button">Info</button>
                                    <button class="btn btn-light rounded-pill bg-gradient" type="button">Light</button>
                                    <button class="btn btn-dark rounded-pill bg-gradient" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Sizes</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Want larger or smaller buttons? Use
                                    <code>.btn-lg</code>
                                    or
                                    <code>.btn-sm</code>
                                    to adjust the button size.
                                </p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <button class="btn btn-primary btn-lg" type="button">Large</button>
                                    <button class="btn btn-info" type="button">Normal</button>
                                    <button class="btn btn-success btn-sm" type="button">Small</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Disabled Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use the
                                    <code>disabled</code>
                                    attribute on a
                                    <code>&lt;button&gt;</code>
                                    to make it inactive and non-interactive.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-info" disabled="" type="button">Info</button>
                                    <button class="btn btn-success" disabled="" type="button">Success</button>
                                    <button class="btn btn-danger" disabled="" type="button">Danger</button>
                                    <button class="btn btn-dark" disabled="" type="button">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Block Button</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted font-14">
                                    To create block-level buttons, add the
                                    <code>.d-grid</code>
                                    class to the parent
                                    <code>&lt;div&gt;</code>
                                    .
                                </p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-primary" type="button">Block Button</button>
                                    <button class="btn btn-lg btn-success" type="button">Block Button</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Toggle Button</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Add
                                    <code>data-bs-toggle="button"</code>
                                    to toggle a button’s
                                    <code>active</code>
                                    state. For pre-toggled buttons, also add
                                    <code>.active</code>
                                    and
                                    <code>aria-pressed="true"</code>
                                    .
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="button" type="button">Toggle button</button>
                                    <button aria-pressed="true" class="btn btn-primary active" data-bs-toggle="button" type="button">Active toggle button</button>
                                    <button class="btn btn-primary" data-bs-toggle="button" disabled="" type="button">Disabled toggle button</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Icon Buttons</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Icon only button. Use it when you want a button with just an icon and no text, ideal for compact UI elements or toolbars.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-primary btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="star"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="leaf"></i>
                                    </button>
                                    <button class="btn btn-warning btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="settings"></i>
                                    </button>
                                    <button class="btn btn-soft-info rounded-circle btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="bell"></i>
                                    </button>
                                    <button class="btn btn-secondary rounded-circle btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="rocket"></i>
                                    </button>
                                    <button class="btn btn-outline-dark rounded-circle btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="plane"></i>
                                    </button>
                                    <button class="btn btn-soft-secondary btn-icon" type="button">
                                        <i class="fs-xl" data-lucide="mic"></i>
                                    </button>
                                    <button class="btn btn-light" type="button">
                                        <i class="fs-xl me-1" data-lucide="hand"></i>
                                        Stop
                                    </button>
                                    <button class="btn btn-dark" type="button">
                                        <i class="fs-xl me-1" data-lucide="zap"></i>
                                        Boost
                                    </button>
                                    <button class="btn btn-outline-info" type="button">
                                        <i class="fs-xl me-1" data-lucide="credit-card"></i>
                                        Payment
                                    </button>
                                    <button class="btn btn-danger" type="button">
                                        <i class="fs-xl me-1" data-lucide="pencil-ruler"></i>
                                        Tools
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-secondary btn-icon" type="button">
                                        <i data-lucide="star"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-icon" type="button">
                                        <i data-lucide="leaf"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn-icon rounded-circle" type="button">
                                        <i data-lucide="settings"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-icon btn-lg" type="button">
                                        <i class="fs-xxl" data-lucide="bell"></i>
                                    </button>
                                    <button class="btn btn-primary btn-icon btn-lg rounded-circle" type="button">
                                        <i class="fs-xxl" data-lucide="rocket"></i>
                                    </button>
                                    <button class="btn btn-success btn-icon btn-lg rounded-circle" type="button">
                                        <i class="fs-xxl" data-lucide="share-2"></i>
                                    </button>
                                    <button class="btn btn-info btn-icon btn-lg" type="button">
                                        <i class="fs-xxl" data-lucide="star"></i>
                                    </button>
                                    <button class="btn btn-warning btn-icon btn-lg" type="button">
                                        <i class="fs-xxl" data-lucide="octagon-alert"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Tags</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>.btn</code>
                                    classes with
                                    <code>&lt;button&gt;</code>
                                    ,
                                    <code>&lt;a&gt;</code>
                                    , or
                                    <code>&lt;input&gt;</code>
                                    elements, though rendering may vary slightly across browsers.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-primary" href="#" role="button">Link</a>
                                    <button class="btn btn-primary" type="submit">Button</button>
                                    <input class="btn btn-primary" type="button" value="Input" />
                                    <input class="btn btn-primary" type="submit" value="Submit" />
                                    <input class="btn btn-primary" type="reset" value="Reset" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Group</h4>
                                </div>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Group multiple buttons together by wrapping them with the
                                    <code>.btn</code>
                                    class inside a
                                    <code>.btn-group</code>
                                    container. This helps align buttons side by side with consistent spacing and styling.
                                </p>
                                <div class="btn-group mb-2">
                                    <button class="btn btn-light" type="button">Left</button>
                                    <button class="btn btn-light" type="button">Middle</button>
                                    <button class="btn btn-light" type="button">Right</button>
                                </div>
                                <br />
                                <div class="btn-group mb-2">
                                    <button class="btn btn-light" type="button">1</button>
                                    <button class="btn btn-light" type="button">2</button>
                                    <button class="btn btn-light" type="button">3</button>
                                    <button class="btn btn-light" type="button">4</button>
                                </div>
                                <div class="btn-group mb-2">
                                    <button class="btn btn-light" type="button">5</button>
                                    <button class="btn btn-light" type="button">6</button>
                                    <button class="btn btn-light" type="button">7</button>
                                </div>
                                <div class="btn-group mb-2">
                                    <button class="btn btn-light" type="button">8</button>
                                </div>
                                <br />
                                <div class="btn-group mb-2">
                                    <button class="btn btn-light" type="button">1</button>
                                    <button class="btn btn-primary" type="button">2</button>
                                    <button class="btn btn-light" type="button">3</button>
                                    <div class="btn-group">
                                        <button aria-expanded="false" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
                                            Dropdown
                                            <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">Dropdown link</a>
                                            <a class="dropdown-item" href="#">Dropdown link</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="btn-group-vertical mb-2">
                                            <button class="btn btn-light" type="button">Top</button>
                                            <button class="btn btn-light" type="button">Middle</button>
                                            <button class="btn btn-light" type="button">Bottom</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="btn-group-vertical mb-2">
                                            <button class="btn btn-light" type="button">Button 1</button>
                                            <button class="btn btn-light" type="button">Button 2</button>
                                            <button aria-expanded="false" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
                                                Button 3
                                                <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                            </div>
                                        </div>
                                    </div>
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
