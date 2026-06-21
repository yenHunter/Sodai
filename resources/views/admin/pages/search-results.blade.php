@extends("shared.base", ["title" => "Search Results"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Pages", "title" => "Search Results"])

                <div class="row">
                    <div class="col-12">
                        <div class="text-center w-md-75 w-xl-50 mx-auto py-3">
                            <div class="app-search app-search-pill input-group mb-3 rounded-pill">
                                <input class="form-control py-2 fw-semibold" placeholder="Search AI platforms..." type="text" value="AI Content Tools" />
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                                <button class="btn btn-secondary" type="button">Discover</button>
                            </div>
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <h5 class="text-muted mb-0">Popular Searches :</h5>
                                <a class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-6" href="#!">Text Generation</a>
                                <a class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-6" href="#!">Image AI</a>
                                <a class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-6" href="#!">Speech</a>
                                <a class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-6" href="#!">Coding</a>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header border-light justify-content-between">
                                <h4 class="fst-italic text-muted mb-0">
                                    Found
                                    <span class="fw-bold badge badge-soft-danger">72</span>
                                    results for
                                    <span class="text-dark">"AI Content Tools"</span>
                                </h4>
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <span class="fw-semibold">Filter By:</span>
                                    <div class="app-search">
                                        <select class="form-select form-control my-1 my-md-0">
                                            <option selected="">Tool Type</option>
                                            <option value="chatbot">Chatbot</option>
                                            <option value="analytics">Analytics</option>
                                            <option value="image">Image Generator</option>
                                            <option value="voice">Voice AI</option>
                                            <option value="automation">Automation</option>
                                        </select>
                                        <i class="app-search-icon text-muted" data-lucide="cpu"></i>
                                    </div>
                                    <div class="app-search">
                                        <select class="form-select form-control my-1 my-md-0">
                                            <option selected="">Pricing</option>
                                            <option value="free">Free</option>
                                            <option value="pro">Pro</option>
                                            <option value="enterprise">Enterprise</option>
                                        </select>
                                        <i class="app-search-icon text-muted" data-lucide="wallet"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-1">
                                        <a class="text-reset" href="#!" target="_blank">NeuroVision AI – Smart Analytics Dashboard</a>
                                    </h4>
                                    <p class="text-success mb-2">https://example.com/neurovision-ai</p>
                                    <p class="text-muted mb-2">
                                        NeuroVision AI is a powerful analytics platform that uses
                                        <span class="fw-semibold">machine learning</span>
                                        to transform raw data into actionable insights, perfect for startups and enterprises.
                                    </p>
                                    <p class="d-flex flex-wrap gap-3 text-muted mb-1 align-items-center fs-base">
                                        <span class="d-flex align-items-center gap-1">
                                            <img alt="avatar-4" class="img-fluid avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                            <a class="link-reset fw-semibold" href="#!">Alex Johnson</a>
                                        </span>
                                        <span>
                                            <i data-lucide="calendar"></i>
                                            Published on: Aug 10, 2025
                                        </span>
                                        <span>
                                            <i data-lucide="users"></i>
                                            Users: 3,200+
                                        </span>
                                        <span>
                                            <i data-lucide="message-circle"></i>
                                            <a class="link-reset" href="#!">Feedback: 145</a>
                                        </span>
                                        <span>
                                            <i data-lucide="star"></i>
                                            Rating: 4.9
                                        </span>
                                    </p>
                                </div>
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-1">
                                        <a class="text-reset" href="#!" target="_blank">SynthChat – AI Conversational Assistant</a>
                                    </h4>
                                    <p class="text-success mb-2">https://example.com/synthchat</p>
                                    <p class="text-muted mb-2">SynthChat is an intelligent chatbot solution designed for customer support and team collaboration, offering natural language understanding and real-time integrations.</p>
                                    <p class="d-flex flex-wrap gap-3 text-muted mb-1 align-items-center fs-base">
                                        <span class="d-flex align-items-center gap-1">
                                            <img alt="avatar-5" class="img-fluid avatar-xs rounded-circle" src="/images/users/user-5.jpg" />
                                            <a class="link-reset fw-semibold" href="#!">Maria Lopez</a>
                                        </span>
                                        <span>
                                            <i data-lucide="calendar"></i>
                                            Published on: Jul 28, 2025
                                        </span>
                                        <span>
                                            <i data-lucide="users"></i>
                                            Users: 2,450+
                                        </span>
                                        <span>
                                            <i data-lucide="message-circle"></i>
                                            <a class="link-reset" href="#!">Feedback: 89</a>
                                        </span>
                                        <span>
                                            <i data-lucide="star"></i>
                                            Rating: 4.7
                                        </span>
                                    </p>
                                </div>
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-1">
                                        <a class="text-reset" href="#!" target="_blank">VisionaryGen – AI Image Creator</a>
                                    </h4>
                                    <p class="text-success mb-2">https://example.com/visionarygen</p>
                                    <p class="text-muted mb-2">VisionaryGen is a creative AI platform that generates stunning images, graphics, and illustrations from text prompts, helping designers and marketers save time and boost creativity.</p>
                                    <p class="d-flex flex-wrap gap-3 text-muted mb-1 align-items-center fs-base">
                                        <span class="d-flex align-items-center gap-1">
                                            <img alt="avatar-6" class="img-fluid avatar-xs rounded-circle" src="/images/users/user-6.jpg" />
                                            <a class="link-reset fw-semibold" href="#!">James Carter</a>
                                        </span>
                                        <span>
                                            <i data-lucide="calendar"></i>
                                            Published on: Jun 15, 2025
                                        </span>
                                        <span>
                                            <i data-lucide="users"></i>
                                            Users: 1,780+
                                        </span>
                                        <span>
                                            <i data-lucide="message-circle"></i>
                                            <a class="link-reset" href="#!">Feedback: 67</a>
                                        </span>
                                        <span>
                                            <i data-lucide="star"></i>
                                            Rating: 4.8
                                        </span>
                                    </p>
                                </div>
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-3">Featured AI Creators:</h4>
                                    <div class="d-flex gap-2">
                                        <div class="avatar">
                                            <img alt="" class="rounded avatar-xl" src="/images/users/user-4.jpg" />
                                        </div>
                                        <div class="avatar">
                                            <img alt="" class="rounded avatar-xl" src="/images/users/user-5.jpg" />
                                        </div>
                                        <div class="avatar">
                                            <img alt="" class="rounded avatar-xl" src="/images/users/user-3.jpg" />
                                        </div>
                                        <div class="avatar">
                                            <img alt="" class="rounded avatar-xl" src="/images/users/user-8.jpg" />
                                        </div>
                                        <div class="avatar">
                                            <img alt="" class="rounded avatar-xl" src="/images/users/user-2.jpg" />
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-1">
                                        <a class="text-reset" href="#!" target="_blank">EchoAI – Voice &amp; Speech Recognition</a>
                                    </h4>
                                    <p class="text-success mb-2">https://example.com/echoai</p>
                                    <p class="text-muted mb-2">EchoAI offers advanced speech recognition and voice synthesis, enabling businesses to build smart assistants, transcription tools, and voice-enabled applications.</p>
                                    <p class="d-flex flex-wrap gap-3 text-muted mb-1 align-items-center fs-base">
                                        <span class="d-flex align-items-center gap-1">
                                            <img alt="avatar-5" class="img-fluid avatar-xs rounded-circle" src="/images/users/user-5.jpg" />
                                            <a class="link-reset fw-semibold" href="#!">Daniel Kim</a>
                                        </span>
                                        <span>
                                            <i data-lucide="calendar"></i>
                                            Published on: May 30, 2025
                                        </span>
                                        <span>
                                            <i data-lucide="users"></i>
                                            Users: 1,120+
                                        </span>
                                        <span>
                                            <i data-lucide="message-circle"></i>
                                            <a class="link-reset" href="#!">Feedback: 54</a>
                                        </span>
                                        <span>
                                            <i data-lucide="star"></i>
                                            Rating: 4.6
                                        </span>
                                    </p>
                                </div>
                                <div class="border-bottom border-dashed px-4 py-3">
                                    <h4 class="fs-md mb-3">People also search for:</h4>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="px-3 py-2 bg-light bg-opacity-50 rounded">
                                            <a class="text-reset fs-md fw-semibold" href="#!">
                                                AI SaaS Platforms
                                                <i class="ms-2 align-middle" data-lucide="search"></i>
                                            </a>
                                        </div>
                                        <div class="px-3 py-2 bg-light bg-opacity-50 rounded">
                                            <a class="text-reset fs-md fw-semibold" href="#!">
                                                AI Code Generators
                                                <i class="ms-2 align-middle" data-lucide="search"></i>
                                            </a>
                                        </div>
                                        <div class="px-3 py-2 bg-light bg-opacity-50 rounded">
                                            <a class="text-reset fs-md fw-semibold" href="#!">
                                                AI Productivity Tools
                                                <i class="ms-2 align-middle" data-lucide="search"></i>
                                            </a>
                                        </div>
                                        <div class="px-3 py-2 bg-light bg-opacity-50 rounded">
                                            <a class="text-reset fs-md fw-semibold" href="#!">
                                                AI for Marketing
                                                <i class="ms-2 align-middle" data-lucide="search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="pagination pagination-rounded pagination-boxed justify-content-center mb-0 py-3">
                                    <li class="page-item previous disabled">
                                        <a class="page-link" href="#">
                                            <i data-lucide="chevron-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">...</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">5</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">6</a>
                                    </li>
                                    <li class="page-item next">
                                        <a class="page-link" href="#">
                                            <i data-lucide="chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
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
