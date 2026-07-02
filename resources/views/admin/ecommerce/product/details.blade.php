@extends('admin.include.vertical', ['title' => 'Product Details'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Product Details'])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            @if ($product->thumbnail)
                                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="img-fluid rounded border" style="max-height:320px;object-fit:cover">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:320px">
                                    <i data-lucide="image" class="text-muted" style="width:64px;height:64px"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="mb-1">{{ $product->name }}</h3>
                                    <p class="text-muted mb-0">{{ $product->short_description }}</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.ecommerce.product.edit', $product) }}" class="btn btn-light">Edit</a>
                                    <form action="{{ route('admin.ecommerce.product.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">SKU</div>
                                    <div class="fw-semibold">{{ $product->sku }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">Category</div>
                                    <div class="fw-semibold">{{ $product->category?->name ?? '—' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">Stock</div>
                                    <div class="fw-semibold">{{ $product->stock_quantity }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">Price</div>
                                    <div class="fw-semibold">${{ number_format($product->price, 2) }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">Sale Price</div>
                                    <div class="fw-semibold">{{ $product->sale_price ? '$' . number_format($product->sale_price, 2) : '—' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-muted small">Status</div>
                                    <div class="fw-semibold"><span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h6 class="fw-semibold">Description</h6>
                                <p class="text-muted mb-0">{{ $product->description ?: 'No description provided.' }}</p>
                            </div>
                            <div>
                                <h6 class="fw-semibold">Tags</h6>
                                @forelse ($product->tags as $tag)
                                    <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-muted">No tags</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
                                                                <td>
                                                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                                                        <div class="avatar avatar-sm">
                                                                            <img alt="avatar-6" class="img-fluid rounded-circle" src="/images/users/user-6.jpg" />
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="text-nowrap fs-sm mb-0 lh-base">David Smith</h5>
                                                                            <p class="text-muted fs-xs mb-0">david.smith@healthstore.com</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-warning fs-lg">
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                    </span>
                                                                    <h5 class="mt-2">Decent, but overpriced</h5>
                                                                    <p class="text-muted fst-italic mb-0">"It does the job, but I feel like it's a little expensive for what it offers."</p>
                                                                </td>
                                                                <td>
                                                                    23 Apr, 2025
                                                                    <small class="text-muted">02:20 PM</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-soft-warning fs-xxs">Pending</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="eye"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                                                        <div class="avatar avatar-sm">
                                                                            <img alt="avatar-3" class="img-fluid rounded-circle" src="/images/users/user-3.jpg" />
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="text-nowrap fs-sm mb-0 lh-base">Alice Johnson</h5>
                                                                            <p class="text-muted fs-xs mb-0">alice.johnson@homesupplies.com</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-warning fs-lg">
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                    </span>
                                                                    <h5 class="mt-2">Amazing quality!</h5>
                                                                    <p class="text-muted fst-italic mb-0">"The TV has incredible picture quality. Totally worth the investment!"</p>
                                                                </td>
                                                                <td>
                                                                    24 Apr, 2025
                                                                    <small class="text-muted">09:15 AM</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-soft-success fs-xxs">Published</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="eye"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                                                        <div class="avatar avatar-sm">
                                                                            <img alt="avatar-2" class="img-fluid rounded-circle" src="/images/users/user-2.jpg" />
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="text-nowrap fs-sm mb-0 lh-base">Michael Green</h5>
                                                                            <p class="text-muted fs-xs mb-0">michael.green@mobileshop.com</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-warning fs-lg">
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                    </span>
                                                                    <h5 class="mt-2">Perfect phone, highly recommended!</h5>
                                                                    <p class="text-muted fst-italic mb-0">"The camera is amazing and the performance is smooth. Definitely the best smartphone I have used!"</p>
                                                                </td>
                                                                <td>
                                                                    25 Apr, 2025
                                                                    <small class="text-muted">11:30 AM</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-soft-success fs-xxs">Published</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="eye"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                                                        <div class="avatar avatar-sm">
                                                                            <img alt="avatar-4" class="img-fluid rounded-circle" src="/images/users/user-4.jpg" />
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="text-nowrap fs-sm mb-0 lh-base">Chris Evans</h5>
                                                                            <p class="text-muted fs-xs mb-0">chris.evans@gamestore.com</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-warning fs-lg">
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                        <i data-lucide="star"></i>
                                                                    </span>
                                                                    <h5 class="mt-2">Great for gaming but heavy</h5>
                                                                    <p class="text-muted fst-italic mb-0">"The performance is amazing, but it's a bit too heavy to carry around all day."</p>
                                                                </td>
                                                                <td>
                                                                    26 Apr, 2025
                                                                    <small class="text-muted">10:00 AM</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-soft-warning fs-xxs">Pending</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="eye"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                                        </a>
                                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#">
                                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="card-footer border-0">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div data-table-pagination-info="reviews"></div>
                                                        <div data-table-pagination=""></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include("admin.include.partials.footer")
        </div>
    </div>

    @include("admin.include.partials.customizer") @include("admin.include.partials.footer-scripts")
@endsection

@section("scripts")
    @vite(["resources/js/pages/custom-table.js"])
@endsection
