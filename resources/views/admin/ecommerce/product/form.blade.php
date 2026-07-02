@php $product = $product ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productName">Product Name <span class="text-danger">*</span></label>
        <input class="form-control" id="productName" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" placeholder="Enter product name" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productSku">SKU <span class="text-danger">*</span></label>
        <input class="form-control" id="productSku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" placeholder="SKU-001" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productCategory">Category</label>
        <select class="form-select" id="productCategory" name="category_id">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productStatus">Status <span class="text-danger">*</span></label>
        <select class="form-select" id="productStatus" name="is_active" required>
            <option value="">Select status</option>
            <option value="active" {{ old('is_active', $product->is_active ?? true) == true || old('is_active', $product->is_active ?? true) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('is_active', $product->is_active ?? true) == false || old('is_active', $product->is_active ?? true) == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productFeatured">Featured</label>
        <select class="form-select" id="productFeatured" name="is_featured">
            <option value="inactive" {{ old('is_featured', $product->is_featured ?? false) == false || old('is_featured', $product->is_featured ?? false) == 'inactive' ? 'selected' : '' }}>No</option>
            <option value="active" {{ old('is_featured', $product->is_featured ?? false) == true || old('is_featured', $product->is_featured ?? false) == 'active' ? 'selected' : '' }}>Yes</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productPrice">Price <span class="text-danger">*</span></label>
        <input class="form-control" id="productPrice" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productSalePrice">Sale Price</label>
        <input class="form-control" id="productSalePrice" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productStock">Stock</label>
        <input class="form-control" id="productStock" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productLowStock">Low Stock Threshold</label>
        <input class="form-control" id="productLowStock" name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productWeight">Weight</label>
        <input class="form-control" id="productWeight" name="weight" type="number" step="0.01" min="0" value="{{ old('weight', $product->weight ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productTags">Tags</label>
        <input class="form-control" id="productTags" name="tags" type="text" value="" placeholder="tag1, tag2">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productShortDescription">Short Description</label>
        <input class="form-control" id="productShortDescription" name="short_description" type="text" value="{{ old('short_description', $product->short_description ?? '') }}" placeholder="Short summary">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productDescription">Description</label>
        <textarea class="form-control" id="productDescription" name="description" rows="4" placeholder="Detailed description">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productThumbnail">Thumbnail Image</label>
        <input class="form-control" id="productThumbnail" name="thumbnail" type="file" accept="image/jpeg,image/jpg,image/png,image/webp">
        @if ($product?->thumbnail)
            <div class="mt-2">
                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="rounded border" style="width:90px;height:90px;object-fit:cover">
            </div>
        @endif
    </div>
</div>
