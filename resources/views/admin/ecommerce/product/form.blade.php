<div class="row">
    {{-- Left Column: Product Information & Images --}}
    <div class="col-xxl-8">

        {{-- Product Information Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Information</h4>
                <p class="text-muted mb-0">Provide the necessary details for this product.</p>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Product Name --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label" for="productName">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input class="form-control @error('name') is-invalid @enderror" id="productName"
                                name="name" placeholder="Enter product name" type="text"
                                value="{{ old('name', $product->name ?? '') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- SKU (Read-only in Edit, Hidden in Create) --}}
                    @if ($product)
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label" for="productSkuDisplay">SKU</label>
                                <input class="form-control bg-light" id="productSkuDisplay" value="{{ $product->sku }}"
                                    type="text" readonly />
                                <small class="text-muted">Auto-generated, cannot be changed</small>
                            </div>
                        </div>
                    @endif

                    {{-- Stock Quantity --}}
                    <div class="col-lg-{{ $product ? '4' : '6' }}">
                        <div class="mb-3">
                            <label class="form-label" for="stockNumber">
                                Stock <span class="text-danger">*</span>
                            </label>
                            <input class="form-control @error('stock_quantity') is-invalid @enderror" id="stockNumber"
                                name="stock_quantity" placeholder="250" type="number"
                                value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0"
                                required />
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Low Stock Threshold --}}
                    <div class="col-lg-{{ $product ? '4' : '6' }}">
                        <div class="mb-3">
                            <label class="form-label" for="lowStockThreshold">
                                Low Stock Threshold <span class="text-danger">*</span>
                            </label>
                            <input class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                id="lowStockThreshold" name="low_stock_threshold" placeholder="5"
                                value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}"
                                type="number" min="0" required />
                            @error('low_stock_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Short Description (Quill) --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">
                                Short Description <span class="text-muted">(Optional)</span>
                            </label>
                            <div id="shortDescriptionEditor" class="quill-editor-container">
                                {!! old('short_description', $product->short_description ?? '') !!}
                            </div>
                            <textarea name="short_description" id="shortDescriptionInput" class="d-none">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                            @error('short_description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description (Quill) --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">
                                Description <span class="text-muted">(Optional)</span>
                            </label>
                            <div id="descriptionEditor" class="quill-editor-container">
                                {!! old('description', $product->description ?? '') !!}
                            </div>
                            <textarea name="description" id="descriptionInput" class="d-none">{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Physical Attributes --}}
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="productWeight">
                                Weight <span class="text-muted">(Optional)</span>
                            </label>
                            <input class="form-control @error('weight') is-invalid @enderror" id="productWeight"
                                name="weight" placeholder="0.00" type="number" step="0.01"
                                value="{{ old('weight', $product->weight ?? '') }}" />
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="weightUnit">
                                Unit <span class="text-muted">(Optional)</span>
                            </label>
                            <select class="form-select @error('weight_unit') is-invalid @enderror" id="weightUnit"
                                name="weight_unit">
                                <option value="">Select Unit</option>
                                <option value="kg"
                                    {{ old('weight_unit', $product->weight_unit ?? '') == 'kg' ? 'selected' : '' }}>kg
                                </option>
                                <option value="g"
                                    {{ old('weight_unit', $product->weight_unit ?? '') == 'g' ? 'selected' : '' }}>g
                                </option>
                                <option value="lb"
                                    {{ old('weight_unit', $product->weight_unit ?? '') == 'lb' ? 'selected' : '' }}>lb
                                </option>
                                <option value="oz"
                                    {{ old('weight_unit', $product->weight_unit ?? '') == 'oz' ? 'selected' : '' }}>oz
                                </option>
                            </select>
                            @error('weight_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="productColor">
                                Color <span class="text-muted">(Optional)</span>
                            </label>
                            <input class="form-control @error('color') is-invalid @enderror" id="productColor"
                                name="color" placeholder="e.g., Blue" type="text"
                                value="{{ old('color', $product->color ?? '') }}" />
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="productSize">
                                Size <span class="text-muted">(Optional)</span>
                            </label>
                            <input class="form-control @error('size') is-invalid @enderror" id="productSize"
                                name="size" placeholder="e.g., Medium" type="text"
                                value="{{ old('size', $product->size ?? '') }}" />
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Product Images Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Images</h4>
                <p class="text-muted mb-0">Upload a thumbnail and optional gallery images.</p>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Thumbnail Dropzone --}}
                    <div class="col-lg-4">
                        <label class="form-label">
                            Thumbnail <span class="text-muted">(Optional)</span>
                        </label>

                        @if ($product && $product->thumbnail)
                            <div class="mb-2">
                                <img src="{{ Storage::url($product->thumbnail) }}" alt="Current Thumbnail"
                                    class="img-thumbnail" style="max-width: 150px;" />
                                <p class="small text-muted mt-1">Current thumbnail (upload new to replace)</p>
                            </div>
                        @endif

                        {{-- ✅ Changed from <form> to <div> — cannot nest forms --}}
                        <div class="dropzone" data-plugin="dropzone" data-previews-container="#thumbnail-previews"
                            data-upload-preview-template="#thumbnailPreviewTemplate" id="thumbnailDropzone">
                            <div class="fallback">
                                <input name="thumbnail" type="file" accept="image/*" />
                            </div>
                            <div class="dz-message needsclick">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                        <i class="fs-24" data-lucide="cloud-upload"></i>
                                    </span>
                                </div>
                                <h5 class="mb-2">Drop thumbnail here</h5>
                                <p class="text-muted mb-3 small">or click to browse</p>
                                <button class="btn btn-sm shadow btn-default" type="button">Browse Image</button>
                            </div>
                        </div>
                        <div class="dropzone-previews mt-3" id="thumbnail-previews"></div>
                        @error('thumbnail')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gallery Dropzone --}}
                    <div class="col-lg-8">
                        <label class="form-label">
                            Gallery Images <span class="text-muted">(Optional, max 10)</span>
                        </label>

                        @if ($product && $product->images->count() > 0)
                            <div class="mb-3">
                                <p class="small text-muted mb-2">Existing gallery images:</p>
                                <div class="row g-2" id="existingGalleryImages">
                                    @foreach ($product->images as $image)
                                        <div class="col-auto" data-image-id="{{ $image->id }}">
                                            <div class="position-relative">
                                                <img src="{{ Storage::url($image->image_path) }}" alt="Gallery Image"
                                                    class="img-thumbnail"
                                                    style="width: 100px; height: 100px; object-fit: cover;" />
                                                @if ($image->is_primary)
                                                    <span class="badge bg-success position-absolute top-0 start-0 m-1">
                                                        Primary
                                                    </span>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary position-absolute top-0 start-0 m-1 set-primary-btn"
                                                        data-image-id="{{ $image->id }}" title="Set as Primary">
                                                        <i data-lucide="star" style="width:12px;height:12px;"></i>
                                                    </button>
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-image-btn"
                                                    data-image-id="{{ $image->id }}" title="Delete Image">
                                                    <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- ✅ Changed from <form> to <div> — cannot nest forms --}}
                        <div class="dropzone" data-plugin="dropzone" data-previews-container="#gallery-previews"
                            data-upload-preview-template="#galleryPreviewTemplate" id="galleryDropzone">
                            <div class="fallback">
                                <input name="images[]" type="file" accept="image/*" multiple />
                            </div>
                            <div class="dz-message needsclick">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                        <i class="fs-24" data-lucide="cloud-upload"></i>
                                    </span>
                                </div>
                                <h4 class="mb-2">Drop files here or click to upload.</h4>
                                <p class="text-muted fst-italic mb-3">You can drag images here, or browse files via the
                                    button below.</p>
                                <button class="btn btn-sm shadow btn-default" type="button">Browse Images</button>
                            </div>
                        </div>
                        <div class="dropzone-previews mt-3" id="gallery-previews"></div>
                        @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Pricing & Organization --}}
    <div class="col-xxl-4">

        {{-- Pricing Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Pricing</h4>
                <p class="text-muted mb-0">Set pricing and discount information.</p>
            </div>
            <div class="card-body">

                {{-- Price --}}
                <div class="mb-3">
                    <label class="form-label" for="price">
                        Price <span class="text-danger">*</span>
                    </label>
                    <div class="app-search">
                        <input class="form-control @error('price') is-invalid @enderror" id="price"
                            name="price" placeholder="199.99" value="{{ old('price', $product->price ?? '') }}"
                            type="number" step="0.01" min="0" required />
                        <i class="app-search-icon text-muted" data-lucide="dollar-sign"></i>
                    </div>
                    @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Purchase Price --}}
                <div class="mb-3">
                    <label class="form-label" for="purchase_price">
                        Purchase Price <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <input class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price"
                            name="purchase_price" placeholder="149.99"
                            value="{{ old('purchase_price', $product->purchase_price ?? '') }}" type="number"
                            step="0.01" min="0" />
                        <i class="app-search-icon text-muted" data-lucide="dollar-sign"></i>
                    </div>
                    @error('purchase_price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Type --}}
                <div class="mb-3">
                    <label class="form-label" for="discount_type">
                        Discount Type <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <select
                            class="form-select form-control my-1 my-md-0 @error('discount_type') is-invalid @enderror"
                            id="discount_type" name="discount_type">
                            <option value="">No Discount</option>
                            <option value="fixed"
                                {{ old('discount_type', $product->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>
                                Fixed Amount
                            </option>
                            <option value="percentage"
                                {{ old('discount_type', $product->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                Percentage
                            </option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="badge-percent"></i>
                    </div>
                    @error('discount_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Discount Value --}}
                <div class="mb-0">
                    <label class="form-label" for="discount_value">
                        Discount Value <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <input class="form-control @error('discount_value') is-invalid @enderror" id="discount_value"
                            name="discount_value" placeholder="10 or 15.50" type="number" step="0.01"
                            min="0" value="{{ old('discount_value', $product->discount_value ?? '') }}" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                    @error('discount_value')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Organization Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Organization</h4>
                <p class="text-muted mb-0">Categorize and organize your product.</p>
            </div>
            <div class="card-body">

                {{-- Brand --}}
                <div class="mb-3">
                    <label class="form-label" for="brand_id">
                        Brand <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0 @error('brand_id') is-invalid @enderror"
                            id="brand_id" name="brand_id">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="layers"></i>
                    </div>
                    @error('brand_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label" for="category_id">
                        Category <span class="text-danger">*</span>
                    </label>
                    <div class="app-search">
                        <select
                            class="form-select form-control my-1 my-md-0 @error('category_id') is-invalid @enderror"
                            id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="layout-grid"></i>
                    </div>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label" for="is_active">
                        Status <span class="text-danger">*</span>
                    </label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0 @error('is_active') is-invalid @enderror"
                            id="is_active" name="is_active" required>
                            <option value="">Select Status</option>
                            <option value="1"
                                {{ old('is_active', $product->is_active ?? '') == '1' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0"
                                {{ old('is_active', $product->is_active ?? '') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="toggle-right"></i>
                    </div>
                    @error('is_active')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Featured --}}
                <div class="mb-3">
                    <label class="form-label" for="is_featured">
                        Featured <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <select
                            class="form-select form-control my-1 my-md-0 @error('is_featured') is-invalid @enderror"
                            id="is_featured" name="is_featured">
                            <option value="0"
                                {{ old('is_featured', $product->is_featured ?? '0') == '0' ? 'selected' : '' }}>
                                Not Featured
                            </option>
                            <option value="1"
                                {{ old('is_featured', $product->is_featured ?? '') == '1' ? 'selected' : '' }}>
                                Featured
                            </option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="star"></i>
                    </div>
                    @error('is_featured')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tags --}}
                <div class="mb-3">
                    <label class="form-label" for="tags">
                        Tags <span class="text-muted">(Optional, comma-separated)</span>
                    </label>
                    <div class="app-search">
                        <input class="form-control @error('tags') is-invalid @enderror" id="tags"
                            name="tags_input" placeholder="modern, comfortable, bestseller" type="text"
                            value="{{ old('tags_input', $product ? $product->tags->pluck('name')->implode(', ') : '') }}" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                    <small class="text-muted">Separate tags with commas</small>
                    @error('tags')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Related Products (Select2) --}}
                <div class="mb-3">
                    <label class="form-label" for="related_products">
                        Related Products <span class="text-muted">(Optional)</span>
                    </label>
                    <select class="form-select @error('related_products') is-invalid @enderror" id="related_products"
                        name="related_products[]" multiple>
                        @if ($product && $product->relatedProducts->count() > 0)
                            @foreach ($product->relatedProducts as $related)
                                <option value="{{ $related->id }}" selected>
                                    {{ $related->name }} ({{ $related->sku }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('related_products')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- SEO Meta Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">SEO Meta</h4>
                <p class="text-muted mb-0">Optimize for search engines.</p>
            </div>
            <div class="card-body">

                {{-- Meta Title --}}
                <div class="mb-3">
                    <label class="form-label" for="meta_title">
                        Meta Title <span class="text-muted">(Optional)</span>
                    </label>
                    <input class="form-control @error('meta.meta_title') is-invalid @enderror" id="meta_title"
                        name="meta[meta_title]" placeholder="SEO-friendly title" type="text" maxlength="255"
                        value="{{ old('meta.meta_title', $product->meta['meta_title'] ?? '') }}" />
                    @error('meta.meta_title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Meta Description --}}
                <div class="mb-3">
                    <label class="form-label" for="meta_description">
                        Meta Description <span class="text-muted">(Optional)</span>
                    </label>
                    <textarea class="form-control @error('meta.meta_description') is-invalid @enderror" id="meta_description"
                        name="meta[meta_description]" placeholder="Brief description for search results" rows="3" maxlength="500">{{ old('meta.meta_description', $product->meta['meta_description'] ?? '') }}</textarea>
                    @error('meta.meta_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Meta Keywords --}}
                <div class="mb-0">
                    <label class="form-label" for="meta_keywords">
                        Meta Keywords <span class="text-muted">(Optional, comma-separated)</span>
                    </label>
                    <input class="form-control @error('meta.meta_keywords') is-invalid @enderror" id="meta_keywords"
                        name="meta[meta_keywords]" placeholder="keyword1, keyword2, keyword3" type="text"
                        maxlength="255"
                        value="{{ old('meta.meta_keywords', $product->meta['meta_keywords'] ?? '') }}" />
                    @error('meta.meta_keywords')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Dropzone Preview Templates --}}
<div class="d-none" id="thumbnailPreviewTemplate">
    <div class="card mt-1 mb-0 border-dashed border">
        <div class="p-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail="" src="#" />
                </div>
                <div class="col ps-0">
                    <a class="fw-semibold" data-dz-name="" href="javascript:void(0);"></a>
                    <p class="mb-0 text-muted small" data-dz-size=""></p>
                </div>
                <div class="col-auto">
                    <a class="btn btn-link btn-lg text-danger" data-dz-remove="" href="javascript:void(0);">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-none" id="galleryPreviewTemplate">
    <div class="card mt-1 mb-0 border-dashed border">
        <div class="p-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail="" src="#" />
                </div>
                <div class="col ps-0">
                    <a class="fw-semibold" data-dz-name="" href="javascript:void(0);"></a>
                    <p class="mb-0 text-muted small" data-dz-size=""></p>
                </div>
                <div class="col-auto">
                    <a class="btn btn-link btn-lg text-danger" data-dz-remove="" href="javascript:void(0);">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
