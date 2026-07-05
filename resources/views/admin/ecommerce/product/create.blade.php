@extends('admin.include.vertical', ['title' => 'Create Product'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Create Product'])
    <div class="row">
        <div class="col-xxl-12">
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
@endsection

@section('scripts')
@endsection
