@extends("admin.include.vertical", ['title' => "Create Product"])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Create Product'])
    <div class="row">
        <div class="col-xxl-12">
            <form action="{{ route('admin.ecommerce.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.ecommerce.product.form', ['categories' => $categories])
                <div class="mt-2 mb-4 d-flex gap-2 justify-content-center">
                    <a class="btn btn-danger fw-semibold" href="#!"> Discard </a>
                    <a class="btn btn-secondary" href="#!"> Save as Draft </a>
                    <a class="btn btn-success" href="#!"> Publish </a>
                </div>
            </form>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/ecommerce-product-add.js'])
@endsection
