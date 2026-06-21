@extends("shared.base", ["title" => "Form Picker"])

@section("styles")
    <link href="{{ asset("plugins/daterangepicker/daterangepicker.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("plugins/pickr/classic.min.css") }}" rel="stylesheet" />
    <link href="{{ asset("plugins/pickr/monolith.min.css") }}" rel="stylesheet" />
    <link href="{{ asset("plugins/pickr/nano.min.css") }}" rel="stylesheet" />
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Forms", "title" => "Pickers"])

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-block">
                                <h4 class="card-title mb-1">Flatpickr</h4>
                                <p class="text-muted mb-0">Lightweight, powerful javascript datetimepicker with no dependencies</p>
                            </div>
                            <div class="card-body">
                                <h4 class="card-title fs-sm fw-bold mb-4">Datepicker</h4>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Basic</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-date-format="d M, Y"</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>DateTime</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-date-format="d.m.y" data-enable-time</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d.m.y" data-enable-time="" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Human-Friendly Dates</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-altFormat="F j, Y"</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control flatpickr-input" data-altformat="F j, Y" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>MinDate and MaxDate</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-date-format="d M, Y" data-minDate="..." data-maxDate="..."</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-maxdate="29 12,2021" data-mindate="25 12, 2021" data-provider="flatpickr" placeholder="Select Date" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Default Date</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-date-format="d M, Y" data-default-date="..."</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-default-date="25 12,2021" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Disabling Dates</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-disable-date="..."</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-disable-date="15 12,2021" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Selecting Multiple Dates</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-multiple-date="true"</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-multiple-date="true" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Range</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-range-date="true"</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-provider="flatpickr" data-range-date="true" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Week Numbers</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-week-number</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-provider="flatpickr" data-week-number="" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Inline</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="flatpickr" data-inline-date="true"</code>
                                            .
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-date-format="d M, Y" data-default-date="25 01,2021" data-inline-date="true" data-provider="flatpickr" type="text" />
                                    </div>
                                </div>
                            </div>
                            <div class="border-top border-dashed"></div>
                            <div class="card-body">
                                <h4 class="card-title fs-sm fw-bold mb-4">Timepicker</h4>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Timepicker</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="timepickr" data-time-basic="true"</code>
                                            attribute.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-provider="timepickr" data-time-basic="true" id="timepicker-example" placeholder="Select Time" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>24-hour Time Picker</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="timepickr" data-time-hrs="true"</code>
                                            attribute.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs" placeholder="Select Time" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Time Picker w/ Limits</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="timepickr" data-min-time="Min.Time" data-max-time="Max.Time"</code>
                                            attribute.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-max-time="16:00" data-min-time="13:00" data-provider="timepickr" placeholder="Select Time" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Preloading Time</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="timepickr" data-default-time="Your Default Time"</code>
                                            attribute.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-default-time="16:45" data-provider="timepickr" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5>Inline</h5>
                                        <p class="text-muted mb-0">
                                            Set
                                            <code>data-provider="timepickr" data-time-inline="Your Default Time"</code>
                                            attribute.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control" data-provider="timepickr" data-time-inline="11:42" type="text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-block">
                                <h4 class="card-title mb-1">Date Range Picker</h4>
                                <p class="text-muted mb-0">A versatile JavaScript component for selecting date ranges, single dates, and times with ease. Perfect for forms and dashboards, it supports calendar views, time pickers, and predefined ranges.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <h5>Date Range</h5>
                                        <p class="text-muted mb-0">Select a custom date range from the calendar.</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control date" data-cancel-class="btn-warning" data-toggle="date-picker" id="singledaterange" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <h5>Date Range Picker With Times</h5>
                                        <p class="text-muted mb-0">Includes both start and end time selection.</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control date" data-locale="{'format': 'DD/MM hh:mm A'}" data-time-picker="true" data-toggle="date-picker" id="daterangetime" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <h5>Single Date Picker</h5>
                                        <p class="text-muted mb-0">Select a single date (e.g., birthday).</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <input class="form-control date" data-single-date-picker="true" data-toggle="date-picker" id="birthdatepicker" type="text" />
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <h5>Predefined Date Ranges</h5>
                                        <p class="text-muted mb-0">Choose from common ranges like "Last 7 Days", etc.</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="position-relative">
                                            <div class="form-control" data-cancel-class="btn-light" data-target-display="#selectedValue" data-toggle="date-picker-range" id="reportrange">
                                                <i class="me-2 align-middle fs-16" data-lucide="calendar"></i>
                                                <span id="selectedValue"></span>
                                                <i class="position-absolute end-0 top-50 translate-middle me-1" data-lucide="chevron-down"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-block">
                                <h4 class="card-title mb-1">Colorpicker</h4>
                                <p class="text-muted mb-0">
                                    Pickr - A simple, multi-themed, responsive and hackable Color-Picker library. No dependencies, no jQuery. Compatible with all CSS Frameworks e.g. Bootstrap, Materialize. Supports alpha channel, rgba, hsla, hsva and more!
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Classic Demo</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>classic-colorpicker</code>
                                            class to set classic colorpicker.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="classic-colorpicker"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Monolith Demo</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>monolith-colorpicker</code>
                                            class to set monolith colorpicker.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="monolith-colorpicker"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Nano Demo</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>nano-colorpicker</code>
                                            class to set nano colorpicker.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="nano-colorpicker"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Demo</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>colorpicker-demo</code>
                                            class to set demo option colorpicker.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="colorpicker-demo"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Picker with Opacity &amp; Hue</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>colorpicker-opacity-hue</code>
                                            class to set colorpicker with opacity &amp; hue.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="colorpicker-opacity-hue"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Switches</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>colorpicker-switch</code>
                                            class to set switch colorpicker.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="colorpicker-switch"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Picker with Input</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>colorpicker-input</code>
                                            class to set colorpicker with input.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="colorpicker-input"></div>
                                    </div>
                                </div>
                                <div class="my-4 border-top border-dashed"></div>
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-semibold mb-1">Color Format</h5>
                                        <p class="text-muted mb-0">
                                            Use
                                            <code>colorpicker-format</code>
                                            class to set colorpicker with format option.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="colorpicker-format"></div>
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
    @vite(["resources/js/pages/form-date-range-picker.js"])
    @vite(["resources/js/pages/form-colorpickr.js"])
@endsection
