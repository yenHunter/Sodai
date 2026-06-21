@extends("shared.base", ["title" => "Clipboard"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["title" => "Clipboard", "subtitle" => "Plugins"])

                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Examples</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy from Element</h5>
                                            <p class="text-muted mb-2">
                                                Use
                                                <code>data-clipboard-target</code>
                                                to copy text from a specific element.
                                            </p>
                                            <p class="text-primary fw-bold" id="copytext">Click the button to copy this promotional text.</p>
                                            <button class="btn btn-sm btn-primary" data-clipboard-target="#copytext">
                                                <i class="me-1" data-lucide="copy"></i>
                                                Copy Text
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Cut from Textarea</h5>
                                            <p class="text-muted mb-2">
                                                Use
                                                <code>data-clipboard-action</code>
                                                with
                                                <code>cut</code>
                                                to remove and copy content.
                                            </p>
                                            <textarea class="form-control" id="cuttext">This content will be cut and removed from this textarea.</textarea>
                                            <button class="btn btn-sm btn-primary mt-3" data-clipboard-action="cut" data-clipboard-target="#cuttext">
                                                <i class="me-1" data-lucide="scissors"></i>
                                                Cut Content
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Email Address</h5>
                                            <p class="text-muted mb-2">Click the button to copy this contact email:</p>
                                            <span class="d-block text-primary fw-bold" id="emailToCopy">support@example.com</span>
                                            <button class="btn btn-sm btn-primary mt-2" data-clipboard-target="#emailToCopy">
                                                <i class="me-1" data-lucide="copy"></i>
                                                Copy Email
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Cut Input Value</h5>
                                            <p class="text-muted mb-2">This cuts the value from a single-line input field.</p>
                                            <input class="form-control" id="cutInput" type="text" value="Temporary token: 8GDF-393K-L99Z" />
                                            <button class="btn btn-sm btn-danger mt-2" data-clipboard-action="cut" data-clipboard-target="#cutInput">
                                                <i class="me-1" data-lucide="scissors"></i>
                                                Cut Token
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Code Snippet</h5>
                                            <p class="text-muted mb-2">Copy this snippet by clicking the button:</p>
                                            <pre><code id="codeSnippet">npm install clipboard --save</code></pre>
                                            <button class="btn btn-sm btn-success mt-2" data-clipboard-target="#codeSnippet">
                                                <i class="me-1" data-lucide="copy"></i>
                                                Copy Command
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy from Input Group</h5>
                                            <p class="text-muted mb-2">Click the copy icon to copy the link here:</p>
                                            <div class="input-group">
                                                <input class="form-control" id="copyLink" readonly="" type="text" value="https://example.com/invite?ref=12345" />
                                                <button class="btn btn-icon btn-secondary" data-clipboard-target="#copyLink" type="button">
                                                    <i class="fs-lg" data-lucide="copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Username</h5>
                                            <p class="text-muted mb-2">Copy a predefined username from a span element.</p>
                                            <span class="d-block text-primary fw-bold" id="copyUsername">john_doe_92</span>
                                            <button class="btn btn-sm btn-primary mt-2" data-clipboard-target="#copyUsername">
                                                <i class="me-1" data-lucide="copy"></i>
                                                Copy Username
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Discount Code</h5>
                                            <p class="text-muted mb-2">Copy a promotional discount code for checkout.</p>
                                            <div class="input-group">
                                                <input class="form-control" id="discountCode" readonly="" type="text" value="SAVE20NOW" />
                                                <button class="btn btn-icon btn-warning" data-clipboard-target="#discountCode" type="button">
                                                    <i class="fs-lg" data-lucide="copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy HTML Template</h5>
                                            <p class="text-muted mb-2">Copy a block of HTML code from a &lt;pre&gt; tag.</p>
                                            <code id="htmlTemplate">&lt;button class="btn btn-primary"&gt;Click Me&lt;/button&gt;</code>
                                            <br />
                                            <button class="btn btn-sm btn-info mt-2" data-clipboard-target="#htmlTemplate">
                                                <i class="me-1" data-lucide="copy"></i>
                                                Copy HTML
                                            </button>
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
    @vite(["resources/js/pages/plugins-clipboard.js"])
@endsection
