@extends("shared.base", ["title" => "Create/Edit Order"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Ecommerce", "title" => "Add/Edit Order"])

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Create Order</h4>
                            </div>
                            <div class="card-body">
                                <form id="orderForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Order ID</label>
                                            <input class="form-control" disabled="" id="orderId" placeholder="#WB20100" type="text" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Order Date</label>
                                            <input class="form-control flatpickr-input" data-altformat="F j, Y" data-provider="flatpickr" id="orderDate" required="" type="datetime-local" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Customer Name</label>
                                            <input class="form-control" id="customerName" placeholder="Mason Carter" required="" type="text" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Customer Email</label>
                                            <input class="form-control" id="customerEmail" placeholder="mason.carter@shopmail.com" required="" type="email" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Product Name</label>
                                            <select class="form-select" id="productName" required="">
                                                <option disabled="" selected="" value="">Select Product</option>
                                                <option value="iPhone 15 Pro">iPhone 15 Pro</option>
                                                <option value="MacBook Air M3">MacBook Air M3</option>
                                                <option value="iPad Pro 13″ (M4)">iPad Pro 13″ (M4)</option>
                                                <option value="Apple Watch Ultra 2">Apple Watch Ultra 2</option>
                                                <option value="AirPods Pro (2nd Gen)">AirPods Pro (2nd Gen)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Quantity</label>
                                            <input class="form-control" id="quantity" min="1" required="" type="number" value="1" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Amount ($)</label>
                                            <input class="form-control" id="amount" placeholder="129.45" required="" step="0.01" type="number" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Payment Status</label>
                                            <select class="form-select" id="paymentStatus" required="">
                                                <option value="Paid">Paid</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Failed">Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Order Status</label>
                                            <select class="form-select" id="orderStatus" required="">
                                                <option value="Ordered">Ordered</option>
                                                <option value="Processing">Processing</option>
                                                <option value="Delivered">Delivered</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Returned">Returned</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Payment Method</label>
                                            <select class="form-select" id="paymentMethod" required="">
                                                <option value="Visa">Visa</option>
                                                <option value="Mastercard">Mastercard</option>
                                                <option value="PayPal">PayPal</option>
                                                <option value="COD">Cash on Delivery</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Card Last 4 Digits</label>
                                            <input class="form-control" id="cardNumber" maxlength="4" placeholder="7832" type="text" />
                                        </div>
                                        <div class="col-12 text-center">
                                            <button class="btn btn-light me-2" data-bs-dismiss="modal" type="button">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Order</button>
                                        </div>
                                    </div>
                                </form>
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
