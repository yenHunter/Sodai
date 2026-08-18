@extends('admin.include.vertical', ['title' => 'Configuration'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Configuration'])

    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">
        @php
            $configCards = [
                [
                    'title' => 'Company',
                    'desc' => 'Set company information and details.',
                    'icon' => 'building-store',
                    'route' => 'admin.settings.company',
                ],
                [
                    'title' => 'Design',
                    'desc' => 'Set logo, favicon and brand color.',
                    'icon' => 'paint',
                    'route' => 'admin.settings.design',
                ],
                [
                    'title' => 'Notifications',
                    'desc' => 'Set store notification preferences.',
                    'icon' => 'bell',
                    'route' => 'admin.settings.notification',
                ],
                [
                    'title' => 'Shipping',
                    'desc' => 'Set shipping rate and free-shipping rules.',
                    'icon' => 'truck-delivery',
                    'route' => 'admin.settings.shipping',
                ],
                [
                    'title' => 'Payment Methods',
                    'desc' => 'Set available payment options.',
                    'icon' => 'credit-card',
                    'route' => 'admin.settings.payment',
                ],
                [
                    'title' => 'Inventory',
                    'desc' => 'Set stock and low-stock behavior.',
                    'icon' => 'package',
                    'route' => 'admin.settings.inventory',
                ],
                [
                    'title' => 'Invoice Settings',
                    'desc' => 'Set invoice numbering and footer note.',
                    'icon' => 'file-invoice',
                    'route' => 'admin.settings.invoice',
                ],
                [
                    'title' => 'Order Settings',
                    'desc' => 'Set order rules and checkout options.',
                    'icon' => 'shopping-cart',
                    'route' => 'admin.settings.order',
                ],
                [
                    'title' => 'Taxes',
                    'desc' => 'Set tax label, rate and calculation mode.',
                    'icon' => 'receipt-tax',
                    'route' => 'admin.settings.tax',
                ],
                [
                    'title' => 'Marketing',
                    'desc' => 'Set default SEO meta and social links.',
                    'icon' => 'speakerphone',
                    'route' => 'admin.settings.marketing',
                ],
            ];
        @endphp

        @foreach ($configCards as $card)
            <div class="col">
                <a href="{{ route($card['route']) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 my-3">
                                <div class="avatar-xxl flex-shrink-0">
                                    <span class="avatar-title text-bg-secondary bg-opacity-90 rounded-circle fs-48">
                                        <i class="ti ti-{{ $card['icon'] }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $card['title'] }}</h4>
                                    <p class="mb-0">{{ $card['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        {{-- Env-based, informational only (no DB-backed page) --}}
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-secondary bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Email <span class="badge bg-secondary-subtle text-secondary ms-1">.env</span>
                            </h4>
                            <p class="mb-0">SMTP credentials are configured via environment variables for security.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-secondary bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-shield-check"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Google Captcha <span
                                    class="badge bg-secondary-subtle text-secondary ms-1">.env</span></h4>
                            <p class="mb-0">reCAPTCHA keys are configured via environment variables for security.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
