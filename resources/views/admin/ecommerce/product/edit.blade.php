@extends('admin.include.vertical', ['title' => 'Edit Product'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Edit Product'])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Product</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ecommerce.product.update', $product) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.ecommerce.product.form', [
                            'categories' => $categories,
                            'product' => $product,
                        ])
                        <div class="mt-2 mb-4 d-flex gap-2 justify-content-center">
                            <a class="btn btn-danger fw-semibold" href="#!"> Discard </a>
                            <button type="submit" class="btn btn-success"> Publish </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
