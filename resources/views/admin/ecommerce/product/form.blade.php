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
                            <label class="form-label" for="lowStockThreshold">Low Stock Threshold <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" id="lowStockThreshold" name="low_stock_threshold"
                                placeholder="Enter low stock threshold"
                                value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? '') }}"
                                type="number" step="1" required />
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
                                            <a class="btn btn-link btn-lg text-danger" data-dz-remove=""
                                                href="">
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
                            data-upload-preview-template="#uploadPreviewTemplate" id="myAwesomeDropzone"
                            method="post">
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
                                            <img alt="" class="avatar-sm rounded bg-light"
                                                data-dz-thumbnail="" src="#" />
                                        </div>
                                        <div class="col ps-0">
                                            <a class="fw-semibold" data-dz-name="" href="javascript:void(0);"></a>
                                            <p class="mb-0 text-muted" data-dz-size=""></p>
                                        </div>
                                        <div class="col-auto">
                                            <a class="btn btn-link btn-lg text-danger" data-dz-remove=""
                                                href="">
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
                    <label class="form-label" for="price">Price <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <input class="form-control" id="price" name="price"
                            placeholder="Enter price (e.g., 199.99)" value="{{ old('price', $product->price ?? '') }}"
                            type="number" required />
                        <i class="app-search-icon text-muted" data-lucide="dollar-sign"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="purchase_price">Purchase Price
                        <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <input class="form-control" id="purchase_price" name="purchase_price"
                            placeholder="Enter purchase price (e.g., 199.99)"
                            value="{{ old('purchase_price', $product->purchase_price ?? '') }}" type="number"
                            required />
                        <i class="app-search-icon text-muted" data-lucide="dollar-sign"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="discount_type">Discount Type
                        <span class="text-muted">(Optional)</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="discount_type"
                            name="discount_type">
                            <option selected disabled>Choose Discount</option>
                            <option value="No Discount">No Discount</option>
                            <option value="Flat Discount">Flat Discount</option>
                            <option value="Percentage Discount">Percentage Discount</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="badge-percent"></i>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="discount_value">Discount Value
                        <span class="text-muted">(Optional)</span></label>
                    <div class="app-search">
                        <input class="form-control" id="discount_value" name="discount_value"
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
                        <select class="form-select form-control my-1 my-md-0" id="brand" name="brand" required>
                            <option selected="">Choose Brand</option>
                            <option value="1">Brand A</option>
                            <option value="2">Brand B</option>
                            <option value="3">Brand C</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="layers"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="category_id" name="category_id" required>
                            <option selected="">Choose Category</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Fashion">Fashion</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="layout-grid"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="is_active">Status <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="is_active" name="is_active" required>
                            <option selected="">Choose Status</option>
                            <option value="1">Published</option>
                            <option value="0">Inactive</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="wand-sparkles"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="is_featured">Featured <span class="text-danger">*</span></label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" id="is_featured" name="is_featured" required>
                            <option selected="">Choose Feature Status</option>
                            <option value="1">Featured</option>
                            <option value="0">Not Featured</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="wand-sparkles"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tags">Tags</label>
                    <div class="app-search">
                        <input class="form-control" id="tags" name="tags" placeholder="Enter tags separated by commas"
                            type="text" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="meta">Meta Tags</label>
                    <div class="app-search">
                        <input class="form-control" id="meta" name="meta" placeholder="Enter meta tags separated by commas"
                            type="text" />
                        <i class="app-search-icon text-muted" data-lucide="tag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
