@extends("shared.base", ["title" => "Seller Details"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Ecommerce", "title" => "Seller Details"])

                <div class="row">
                    <div class="col-xl-3">
                        <div class="card card-top-sticky">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center rounded border-light p-2 bg-light-subtle border">
                                    <div class="d-flex justify-content-start align-items-center gap-3">
                                        <div class="avatar avatar-xl">
                                            <img alt="avatar-store" class="img-fluid rounded-circle" src="/images/sellers/1.png" />
                                        </div>
                                        <div>
                                            <h5 class="text-nowrap fw-bold mb-1">MacHub Retailers</h5>
                                            <p class="text-muted mb-0">Since 2017</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-icon btn-light" data-bs-toggle="dropdown">
                                            <i class="fs-24" data-lucide="ellipsis"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#">Edit Profile</a></li>
                                            <li><a class="dropdown-item text-danger" href="#">Report</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="user"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">Owner: <span class="fw-semibold">Sophie Martinez</span></p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="briefcase"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">Business Type: <span class="fw-semibold">Fashion Retail</span></p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="calendar-check"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">Founded: <span class="fw-semibold">2015</span></p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="map-pin"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">Location: <span class="fw-semibold">Los Angeles, USA</span></p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="mail"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">
                                            Support:
                                            <a class="text-primary fw-semibold" href="mailto:help@stylehub.com">help@stylehub.com</a>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                            <i class="fs-xl text-muted" data-lucide="earth"></i>
                                        </div>
                                        <p class="mb-0 fs-sm">
                                            Website:
                                            <a class="text-primary fw-semibold" href="#!">www.stylehub.com</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body border-top border-dashed">
                                <h5 class="mb-3">Contact Seller</h5>
                                <form action="#">
                                    <div class="mb-3">
                                        <textarea class="form-control" id="exampleFormControlTextarea1" placeholder="Enter your messages..." rows="4"></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-primary" type="submit">Send Messages</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 title="Number of Orders">Orders</h5>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 my-2">
                                            <div class="avatar-md flex-shrink-0">
                                                <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-22">
                                                    <i data-lucide="shopping-cart"></i>
                                                </span>
                                            </div>
                                            <h3 class="mb-0">1,250</h3>
                                        </div>
                                        <p class="mb-0">
                                            <span class="text-success"><i data-lucide="circle"></i></span>
                                            <span class="text-nowrap text-muted">Total Orders</span>
                                            <span class="float-end"><b>15,320</b></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 title="Revenue Earned">Revenue</h5>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 my-2">
                                            <div class="avatar-md flex-shrink-0">
                                                <span class="avatar-title text-bg-warning bg-opacity-90 rounded-circle fs-22">
                                                    <i data-lucide="dollar-sign"></i>
                                                </span>
                                            </div>
                                            <h3 class="mb-0">$98.7k</h3>
                                        </div>
                                        <p class="mb-0">
                                            <span class="text-primary"><i data-lucide="circle"></i></span>
                                            <span class="text-nowrap text-muted">Total Revenue</span>
                                            <span class="float-end"><b>$1.2M</b></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 title="Seller Ratings">Ratings</h5>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 my-2">
                                            <div class="avatar-md flex-shrink-0">
                                                <span class="avatar-title text-bg-info bg-opacity-90 rounded-circle fs-22">
                                                    <i data-lucide="star"></i>
                                                </span>
                                            </div>
                                            <h3 class="mb-0">4.8</h3>
                                        </div>
                                        <p class="mb-0">
                                            <span class="text-info"><i data-lucide="circle"></i></span>
                                            <span class="text-nowrap text-muted">Average Rating</span>
                                            <span class="float-end"><b>5k Reviews</b></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 title="Total Products">Products</h5>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 my-2">
                                            <div class="avatar-md flex-shrink-0">
                                                <span class="avatar-title text-bg-secondary bg-opacity-90 rounded-circle fs-22">
                                                    <i data-lucide="box"></i>
                                                </span>
                                            </div>
                                            <h3 class="mb-0">350</h3>
                                        </div>
                                        <p class="mb-0">
                                            <span class="text-secondary"><i data-lucide="circle"></i></span>
                                            <span class="text-nowrap text-muted">Total Products</span>
                                            <span class="float-end"><b>750 Variants</b></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Seller Overview</h4>
                            </div>
                            <div class="card-body pb-0">
                                <div id="seller-revenue"></div>
                            </div>
                        </div>
                        <h4 class="my-4">My Products</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="card" data-table="" data-table-rows-per-page="8">
                                    <div class="card-header border-light justify-content-between">
                                        <div class="d-flex gap-2">
                                            <div class="app-search">
                                                <input class="form-control" data-table-search="" placeholder="Search product name..." type="search" />
                                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                                            </div>
                                            <button class="btn btn-danger d-none" data-table-delete-selected="">Delete</button>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <div>
                                                <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="15">15</option>
                                                    <option value="20">20</option>
                                                </select>
                                            </div>
                                            <a class="btn btn-danger ms-1" href="{{ url("/apps/ecommerce/product-add") }}"> <i class="fs-sm me-2" data-lucide="plus"></i> Add Product </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                                <tr class="text-uppercase fs-xxs">
                                                    <th class="ps-3" style="width: 1%">
                                                        <input class="form-check-input form-check-input-light fs-14 mt-0" data-table-select-all="" id="select-all-products" type="checkbox" value="option" />
                                                    </th>
                                                    <th data-table-sort="product">Product</th>
                                                    <th data-table-sort="">Category</th>
                                                    <th data-table-sort="">Stock</th>
                                                    <th data-table-sort="">Price</th>
                                                    <th data-table-sort="">Orders</th>
                                                    <th data-table-sort="">Status</th>
                                                    <th class="text-center" style="width: 1%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/1.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Gaming Laptop Pro</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Computers</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">15</h5>
                                                    </td>
                                                    <td>$1,299.00</td>
                                                    <td>45</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/2.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Vintage Leather Wallet</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Accessories</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">78</h5>
                                                    </td>
                                                    <td>$49.95</td>
                                                    <td>210</td>
                                                    <td><span class="badge badge-soft-warning fs-xxs">Pending</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/3.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Men's Running Shoes</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Fashion</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">120</h5>
                                                    </td>
                                                    <td>$89.00</td>
                                                    <td>231</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/4.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Fitness Tracker Watch</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Fitness</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">78</h5>
                                                    </td>
                                                    <td>$49.95</td>
                                                    <td>198</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/5.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Gaming Mouse RGB</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Gaming</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">120</h5>
                                                    </td>
                                                    <td>$29.99</td>
                                                    <td>243</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/6.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Modern Lounge Chair</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Furniture</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">24</h5>
                                                    </td>
                                                    <td>$199.00</td>
                                                    <td>38</td>
                                                    <td><span class="badge badge-soft-danger fs-xxs">Out of Stock</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/7.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Plush Toy Bear</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Toys</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">150</h5>
                                                    </td>
                                                    <td>$15.99</td>
                                                    <td>305</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/8.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">55" Ultra HD Smart TV</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Electronics</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">64</h5>
                                                    </td>
                                                    <td>$499.00</td>
                                                    <td>142</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/9.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Apple iMac 24" M3</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Computers</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">18</h5>
                                                    </td>
                                                    <td>$1,399.00</td>
                                                    <td>29</td>
                                                    <td><span class="badge badge-soft-warning fs-xxs">Pending</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option" />
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-md me-3">
                                                                <img alt="Product" class="img-fluid rounded" src="/images/products/10.png" />
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-0">
                                                                    <a class="link-reset" data-sort="product" href="{{ url("/apps/ecommerce/product-details") }}">Smart Watch Pro X2</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Wearables</td>
                                                    <td>
                                                        <h5 class="fs-base mb-0 fw-medium">85</h5>
                                                    </td>
                                                    <td>$149.50</td>
                                                    <td>197</td>
                                                    <td><span class="badge badge-soft-success fs-xxs">Published</span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div data-table-pagination-info="products"></div>
                                            <div data-table-pagination=""></div>
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
    @vite(["resources/js/pages/custom-table.js"])
    @vite(["resources/js/pages/ecommerce-seller-details.js"])
@endsection
