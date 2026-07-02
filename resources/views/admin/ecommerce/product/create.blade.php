@extends('admin.include.vertical', ['title' => 'Create Product'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Create Product'])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">New Product</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ecommerce.product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('admin.ecommerce.product.form', ['categories' => $categories])
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                            <a href="{{ route('admin.ecommerce.product.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
