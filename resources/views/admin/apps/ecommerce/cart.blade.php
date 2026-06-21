@extends("shared.base", ["title" => "Shopping Cart"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Ecommerce", "title" => "Cart"])

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info border border-info border-opacity-25">
                            <h6 class="mb-2">Buy <span class="fw-bold text-warning">$250</span> more to get <span class="fw-semibold">Free Shipping</span></h6>
                            <div class="progress" style="height: 4px">
                                <div class="progress-bar bg-warning" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title flex-grow-1">Shopping Cart</h4>
                                <a class="text-decoration-underline link-offset-2 fw-medium" href="#">Clear cart</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-custom align-middle mb-0">
                                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                            <tr class="text-uppercase fs-xxs">
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="d-flex align-items-center">
                                                    <img alt="iPhone" class="me-3 rounded" src="/images/products/1.png" width="60" />
                                                    <div>
                                                        <h6 class="mb-1 fs-sm">Apple iPhone 14 128GB</h6>
                                                        <small class="text-muted d-block">Color: White</small>
                                                        <small class="text-muted d-block">Model: 128GB</small>
                                                    </div>
                                                </td>
                                                <td>$899.00</td>
                                                <td>
                                                    <div class="input-group" data-touchspin="" style="max-width: 130px">
                                                        <button class="btn btn-primary floating" data-minus="" type="button">
                                                            <i data-lucide="minus"></i>
                                                        </button>
                                                        <input class="form-control form-control-sm border-0" max="800000" type="number" value="1" />
                                                        <button class="btn btn-primary floating" data-plus="" type="button">
                                                            <i data-lucide="plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold">$899.00</td>
                                                <td>
                                                    <a class="text-muted" href="#"><i class="fs-lg" data-lucide="x"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="d-flex align-items-center">
                                                    <img alt="iPad" class="me-3 rounded" src="/images/products/2.png" width="60" />
                                                    <div>
                                                        <span class="badge bg-danger-subtle text-danger mb-1">-10%</span>
                                                        <h6 class="mb-1 fs-sm">Tablet Apple iPad Pro M2</h6>
                                                        <small class="text-muted d-block">Color: Black</small>
                                                        <small class="text-muted d-block">Model: 256GB</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold">$989.00</span><br />
                                                    <small class="text-decoration-line-through text-muted">$1,099.00</small>
                                                </td>
                                                <td>
                                                    <div class="input-group" data-touchspin="" style="max-width: 130px">
                                                        <button class="btn btn-primary floating" data-minus="" type="button">
                                                            <i data-lucide="minus"></i>
                                                        </button>
                                                        <input class="form-control form-control-sm border-0" max="800000" type="number" value="3" />
                                                        <button class="btn btn-primary floating" data-plus="" type="button">
                                                            <i data-lucide="plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold">$989.00</td>
                                                <td>
                                                    <a class="text-muted" href="#"><i class="fs-lg" data-lucide="x"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="d-flex align-items-center">
                                                    <img alt="Watch" class="me-3 rounded" src="/images/products/3.png" width="60" />
                                                    <div>
                                                        <h6 class="mb-1 fs-sm">Smart Watch Series 7</h6>
                                                        <small class="text-muted d-block">Color: White</small>
                                                        <small class="text-muted d-block">Model: 44mm</small>
                                                    </div>
                                                </td>
                                                <td>$429.00</td>
                                                <td>
                                                    <div class="input-group" data-touchspin="" style="max-width: 130px">
                                                        <button class="btn btn-primary floating" data-minus="" type="button">
                                                            <i data-lucide="minus"></i>
                                                        </button>
                                                        <input class="form-control form-control-sm border-0" max="800000" type="number" value="2" />
                                                        <button class="btn btn-primary floating" data-plus="" type="button">
                                                            <i data-lucide="plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold">$429.00</td>
                                                <td>
                                                    <a class="text-muted" href="#"><i class="fs-lg" data-lucide="x"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <a class="fw-medium d-inline-flex align-items-center gap-1" href="{{ url("/apps/ecommerce/products-grid") }}"> <i data-lucide="arrow-left"></i> Continue Shopping </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Order Summary</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-3">
                                    <li class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal (3 items):</span>
                                        <span>$2,427.00</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Saving:</span>
                                        <span class="text-danger">- $110.00</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tax collected:</span>
                                        <span>$73.40</span>
                                    </li>
                                    <li class="d-flex justify-content-between border-bottom pb-3 mb-3">
                                        <span class="text-muted">Shipping:</span>
                                        <span>Calculated at checkout</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <h6 class="text-uppercase text-muted">Estimated total:</h6>
                                        <h5 class="fw-bold">$2,390.40</h5>
                                    </li>
                                </ul>
                                <a class="btn btn-lg btn-danger w-100 mb-3" href="apps-ecommerce-checkout.html"> Proceed to Checkout </a>
                                <p class="text-muted text-center mb-0"><a class="fw-semibold" href="#">Create an account</a> and get 239 bonuses</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Apply Coupon Code</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning fs-xs py-2">Use <span class="fw-bold">ADMINPRO</span> to get 10% off.</div>
                                <div class="input-group">
                                    <input class="form-control" placeholder="Enter code" type="text" />
                                    <button class="btn btn-primary" type="button">Apply</button>
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
