@extends('admin.include.vertical', ['title' => 'Create Product'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Create Product'])

    {{-- Session Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="circle-check"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="triangle-alert"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xxl-12">
            <form action="{{ route('admin.ecommerce.product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                @include('admin.ecommerce.product.form', [
                    'categories' => $categories,
                    'brands' => $brands,
                    'product' => null,
                ])
                <div class="mt-2 mb-4 d-flex gap-2 justify-content-center">
                    <a class="btn btn-danger fw-semibold" href="{{ route('admin.ecommerce.product.index') }}"> 
                        Discard 
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn"> 
                        <i data-lucide="save" class="me-1" style="width:16px;height:16px;"></i>
                        Publish Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-product-create.js'])
@endsection