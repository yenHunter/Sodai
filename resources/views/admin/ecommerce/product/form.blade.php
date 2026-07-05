@php $product = $product ?? null; @endphp


<div class="row">
    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Information</h4>
                <p class="text-muted mb-0">To add a new product, please provide the necessary details in the
                    fields below.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label" for="productName">Product Name <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" id="productName" name="name" placeholder="Enter product name"
                                type="text" value="{{ old('name', $product->name ?? '') }}" required />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="productSku">SKU <span class="text-danger">*</span></label>
                            <input class="form-control" id="productSku" name="sku" placeholder="SOFA-10058"
                                value="{{ old('sku', $product->sku ?? '') }}" type="text" required />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="stockNumber">Stock <span class="text-danger">*</span></label>
                            <input class="form-control" id="stockNumber" name="stock_quantity" placeholder="250"
                                type="number" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}"
                                required />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="lowStockThreshold">Low Stock Threshold <span class="text-danger">*</span></label>
                            <input class="form-control" id="lowStockThreshold" name="low_stock_threshold"
                                placeholder="Enter low stock threshold"
                                value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? '') }}" type="number" step="1" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <div>
                            <label class="form-label">Short Description <span
                                    class="text-muted">(Optional)</span></label>
                            <div id="snow-editor">
                                <p>
                                    Introducing the <strong><em>Azure Comfort Single Sofa</em></strong>, a
                                    perfect blend of modern design and luxurious comfort.
                                </p>
                                <p>This premium blue single sofa is designed to elevate any living space with
                                    its sleek profile and rich, durable fabric. It’s the perfect seating option
                                    for your living room, lounge area, or cozy reading nook.</p>
                                <ul>
                                    <li>Crafted with a solid mahogany frame for enhanced durability.</li>
                                    <li>Upholstered in a high-quality blue fabric that offers both style and
                                        comfort.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div>
                            <label class="form-label">Description <span class="text-muted">(Optional)</span></label>
                            <div id="snow-editor">
                                <p>
                                    Introducing the <strong><em>Azure Comfort Single Sofa</em></strong>, a
                                    perfect blend of modern design and luxurious comfort.
                                </p>
                                <p>This premium blue single sofa is designed to elevate any living space with
                                    its sleek profile and rich, durable fabric. It’s the perfect seating option
                                    for your living room, lounge area, or cozy reading nook.</p>
                                <ul>
                                    <li>Crafted with a solid mahogany frame for enhanced durability.</li>
                                    <li>Upholstered in a high-quality blue fabric that offers both style and
                                        comfort.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Image</h4>
                <p class="text-muted mb-0">To upload a product image, please use the option below to select and
                    upload the relevant file.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <label class="form-label">Thumbnail <span class="text-muted">(Optional)</span></label>
                        <form action="/" class="dropzone" data-plugin="dropzone"
                            data-previews-container="#file-previews"
                            data-upload-preview-template="#uploadPreviewTemplate" id="myAwesomeDropzone" method="post">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            <div class="dz-message needsclick">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                        <i class="fs-24" data-lucide="cloud-upload"></i>
                                    </span>
                                </div>
                                <h4 class="mb-2">Drop files here or click to upload.</h4>
                                <p class="text-muted fst-italic mb-3">You can drag images here, or browse files
                                    via the button below.</p>
                                <button class="btn btn-sm shadow btn-default" type="button">Browse
                                    Images</button>
                            </div>
                        </form>
                        <div class="dropzone-previews mt-3" id="file-previews"></div>
                        <div class="d-none" id="uploadPreviewTemplate">
                            <div class="card mt-1 mb-0 border-dashed border">
                                <div class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail=""
                                                src="#" />
                                        </div>
                                        <div class="col ps-0">
                                            <a class="fw-semibold" data-dz-name="" href="javascript:void(0);"></a>
                                            <p class="mb-0 text-muted" data-dz-size=""></p>
                                        </div>
                                        <div class="col-auto">
                                            <a class="btn btn-link btn-lg text-danger" data-dz-remove="" href="">
                                                <span class="dropzone-close"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <label class="form-label">Gallery Images <span class="text-muted">(Optional)</span></label>
                        <form action="/" class="dropzone" data-plugin="dropzone"
                            data-previews-container="#file-previews"
                            data-upload-preview-template="#uploadPreviewTemplate" id="myAwesomeDropzone" method="post">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            <div class="dz-message needsclick">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                        <i class="fs-24" data-lucide="cloud-upload"></i>
                                    </span>
                                </div>
                                <h4 class="mb-2">Drop files here or click to upload.</h4>
                                <p class="text-muted fst-italic mb-3">You can drag images here, or browse files
                                    via the button below.</p>
                                <button class="btn btn-sm shadow btn-default" type="button">Browse
                                    Images</button>
                            </div>
                        </form>
                        <div class="dropzone-previews mt-3" id="file-previews"></div>
                        <div class="d-none" id="uploadPreviewTemplate">
                            <div class="card mt-1 mb-0 border-dashed border">
                                <div class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail=""
                                                src="#" />
                                        </div>
                                        <div class="col ps-0">
                                            <a class="fw-semibold" data-dz-name="" href="javascript:void(0);"></a>
                                            <p class="mb-0 text-muted" data-dz-size=""></p>
                                        </div>
                                        <div class="col-auto">
                                            <a class="btn btn-link btn-lg text-danger" data-dz-remove="" href="">
                                                <span class="dropzone-close"></span>
                                            </a>
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
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Pricing</h4>
                <p class="text-muted mb-0">Set the base price and applicable discount for the product using the
                    options below.</p>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="basePrice">Base Price <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <input class="form-control" id="basePrice" name="price"
                            placeholder="Enter base price (e.g., 199.99)"
                            value="{{ old('price', $product->price ?? '') }}" type="number" required />
                        <i class="app-search-icon text-muted" data-lucide="dollar-sign"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="discount">Discount Type <span
                            class="text-muted">(Optional)</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="discount">
                            <option selected="">Choose Discount</option>
                            <option value="No Discount">No Discount</option>
                            <option value="Flat Discount">Flat Discount</option>
                            <option value="Percentage Discount">Percentage Discount</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="badge-percent"></i>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="discountValue">Discount Value <span
                            class="text-muted">(Optional)</span></label>
                    <div class="app-search">
                        <input class="form-control" id="discountValue"
                            placeholder="Enter discount amount or percentage" type="number" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Organize</h4>
                <p class="text-muted mb-0">Organize your product by selecting the appropriate brand, category,
                    sub-category, status, and tags.</p>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="brand">Brand</label>
                    <div class="app-search">
                        <input class="form-control" id="brand" placeholder="Enter brand name" type="text" />
                        <i class="app-search-icon text-muted" data-lucide="layers"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category">Category <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="category">
                            <option selected="">Choose Category</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Fashion">Fashion</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="layout-grid"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="subCategory">Sub Category <span
                            class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="subCategory">
                            <option selected="">Choose Sub Category</option>
                            <option value="Chairs">Chairs</option>
                            <option value="Sofas">Sofas</option>
                            <option value="Tables">Tables</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="list-check"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="statusOne">Status <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="statusOne">
                            <option selected="">Choose Status</option>
                            <option value="Published">Published</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Schedule">Schedule</option>
                            <option value="Draft">Draft</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="wand-sparkles"></i>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="tags">Tags</label>
                    <div class="app-search">
                        <input class="form-control" id="tags" placeholder="Enter tags separated by commas"
                            type="text" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-2 mb-4 d-flex gap-2 justify-content-center">
    <a class="btn btn-danger fw-semibold" href="#!"> Discard </a>
    <a class="btn btn-secondary" href="#!"> Save as Draft </a>
    <a class="btn btn-success" href="#!"> Publish </a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productName">Product Name <span
                class="text-danger">*</span></label>
        <input class="form-control" id="productName" name="name" type="text"
            value="{{ old('name', $product->name ?? '') }}" placeholder="Enter product name" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productSku">SKU <span class="text-danger">*</span></label>
        <input class="form-control" id="productSku" name="sku" type="text"
            value="{{ old('sku', $product->sku ?? '') }}" placeholder="SKU-001" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productCategory">Category</label>
        <select class="form-select" id="productCategory" name="category_id">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productStatus">Status <span class="text-danger">*</span></label>
        <select class="form-select" id="productStatus" name="is_active" required>
            <option value="">Select status</option>
            <option value="active"
                {{ old('is_active', $product->is_active ?? true) == true || old('is_active', $product->is_active ?? true) == 'active' ? 'selected' : '' }}>
                Active</option>
            <option value="inactive"
                {{ old('is_active', $product->is_active ?? true) == false || old('is_active', $product->is_active ?? true) == 'inactive' ? 'selected' : '' }}>
                Inactive</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productFeatured">Featured</label>
        <select class="form-select" id="productFeatured" name="is_featured">
            <option value="inactive"
                {{ old('is_featured', $product->is_featured ?? false) == false || old('is_featured', $product->is_featured ?? false) == 'inactive' ? 'selected' : '' }}>
                No</option>
            <option value="active"
                {{ old('is_featured', $product->is_featured ?? false) == true || old('is_featured', $product->is_featured ?? false) == 'active' ? 'selected' : '' }}>
                Yes</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productPrice">Price <span class="text-danger">*</span></label>
        <input class="form-control" id="productPrice" name="price" type="number" step="0.01" min="0"
            value="{{ old('price', $product->price ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productSalePrice">Sale Price</label>
        <input class="form-control" id="productSalePrice" name="sale_price" type="number" step="0.01"
            min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productStock">Stock</label>
        <input class="form-control" id="productStock" name="stock_quantity" type="number" min="0"
            value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productLowStock">Low Stock Threshold</label>
        <input class="form-control" id="productLowStock" name="low_stock_threshold" type="number" min="0"
            value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productWeight">Weight</label>
        <input class="form-control" id="productWeight" name="weight" type="number" step="0.01" min="0"
            value="{{ old('weight', $product->weight ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="productTags">Tags</label>
        <input class="form-control" id="productTags" name="tags" type="text" value=""
            placeholder="tag1, tag2">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productShortDescription">Short Description</label>
        <input class="form-control" id="productShortDescription" name="short_description" type="text"
            value="{{ old('short_description', $product->short_description ?? '') }}" placeholder="Short summary">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productDescription">Description</label>
        <textarea class="form-control" id="productDescription" name="description" rows="4"
            placeholder="Detailed description">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold" for="productThumbnail">Thumbnail Image</label>
        <input class="form-control" id="productThumbnail" name="thumbnail" type="file"
            accept="image/jpeg,image/jpg,image/png,image/webp">
        @if ($product?->thumbnail)
            <div class="mt-2">
                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                    class="rounded border" style="width:90px;height:90px;object-fit:cover">
            </div>
        @endif
    </div>
</div>
