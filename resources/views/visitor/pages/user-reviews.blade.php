@extends('visitor.layout.app', ['title' => 'Sodai - My Reviews', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12"><h2 class="ec-breadcrumb-title">My Reviews</h2></div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Reviews</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                @include('visitor.partials.account-sidebar')

                <div class="ec-shop-rightside col-lg-9 col-md-12">

                    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                    @if ($errors->any())
                        <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                    @endif

                    @if ($reviewable->isNotEmpty())
                        <div class="ec-vendor-dashboard-card">
                            <div class="ec-vendor-card-header"><h5>Products You Can Review</h5></div>
                            <div class="ec-vendor-card-body">
                                @foreach ($reviewable as $item)
                                    <div class="row border-bottom py-2 align-items-center">
                                        <div class="col-md-6">{{ $item->product?->name ?? 'Product' }}</div>
                                        <div class="col-md-6 text-md-end">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal"
                                                data-order-id="{{ $item->order_id }}" data-product-id="{{ $item->product_id }}"
                                                data-product-name="{{ $item->product?->name }}">
                                                Write a Review
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header"><h5>My Reviews</h5></div>
                        <div class="ec-vendor-card-body">
                            @forelse ($reviews as $review)
                                <div class="row border-bottom py-3">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">{{ $review->product?->name ?? 'Product removed' }}</h6>
                                        <span class="text-warning">
                                            @for ($i = 0; $i < $review->rating; $i++)★@endfor
                                        </span>
                                        <span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }} ms-2">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                        <p class="mb-0 mt-1">{{ $review->comment }}</p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        @if ($review->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editReviewModal"
                                                data-id="{{ $review->id }}" data-rating="{{ $review->rating }}" data-comment="{{ $review->comment }}"
                                                data-update-url="{{ route('account.reviews.update', $review) }}">
                                                Edit
                                            </button>
                                        @endif
                                        <form action="{{ route('account.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this review?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">You haven't written any reviews yet.</p>
                            @endforelse
                        </div>
                        @if ($reviews->hasPages())
                            <div class="p-3">{{ $reviews->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Write Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Write a Review — <span id="reviewProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('account.reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="reviewOrderId">
                    <input type="hidden" name="product_id" id="reviewProductId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Select rating</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Comment (Optional)</label>
                            <textarea name="comment" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editReviewForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" id="editReviewRating" class="form-select" required>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" id="editReviewComment" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-account-reviews.js'])
@endsection