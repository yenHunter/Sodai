<div class="row">
    {{-- Left Column: Product Information, Variants & Images --}}
    <div class="col-xxl-8">

        {{-- Product Information Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Information</h4>
                <p class="text-muted mb-0">Provide the necessary details for this product.</p>
            </div>
            <div class="card-body">
                <div class="row">
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

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">
                                Short Description <span class="text-muted">(Optional)</span>
                            </label>
                            <textarea name="short_description" id="short_description"
                                class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                            @error('short_description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

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
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             VARIANTS CARD — the core of the new schema.
             Every product has >=1 variant. Simple products
             (water glass) get exactly 1 with no option values.
             Configurable products (t-shirt, phone) get N rows.
        ═══════════════════════════════════════════ --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between p-3">
                <div>
                    <h4 class="card-title mb-1">Variants</h4>
                    <p class="text-muted mb-0">
                        Add options (Color, Size, Weight...) if this product comes in multiple
                        versions. Leave options empty for a simple, single-SKU product.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                    <i data-lucide="plus" class="fs-sm me-1"></i> Add Variant
                </button>
            </div>
            <div class="card-body">

                {{-- Option builder: lets admin define e.g. "Color: Red, Blue" then generate rows --}}
                <div class="border rounded p-3 mb-3 bg-light-subtle">
                    <label class="form-label fw-semibold mb-2">Quick Option Builder <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <div class="row g-2 align-items-end" id="optionBuilderRow">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="optionBuilderName"
                                placeholder="Option name (e.g. Color)">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="optionBuilderValues"
                                placeholder="Comma separated (e.g. Red, Blue, Green)">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100"
                                id="generateVariantsBtn">
                                <i data-lucide="grid-3x3" class="fs-sm me-1"></i> Generate Combinations
                            </button>
                        </div>
                        <div class="col-12 d-none" id="optionBuilderSwatchesWrapper">
                            <label class="form-label mb-1 small fw-semibold">
                                Color Swatches <span class="text-muted fw-normal">(hex codes, comma separated, same
                                    order as values above)</span>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="optionBuilderSwatches"
                                placeholder="#ff0000, #0000ff, #00ff00">
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Add multiple options (Color, then Size) before generating to build a full matrix (e.g. 3 colors
                        × 4 sizes = 12 variants).
                    </small>
                </div>

                <div id="variantsContainer">
                    {{-- Variant rows injected here by JS / rendered server-side on edit --}}
                    @if ($product && $product->variants->isNotEmpty())
                        @foreach ($product->variants as $vIndex => $variant)
                            @include('admin.ecommerce.product.variant-row', [
                                'index' => $vIndex,
                                'variant' => $variant,
                            ])
                        @endforeach
                    @else
                        @include('admin.ecommerce.product.variant-row', [
                            'index' => 0,
                            'variant' => null,
                        ])
                    @endif
                </div>

                @error('variants')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Product-level Images Card (shared gallery, not tied to a variant) --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Product Images</h4>
                <p class="text-muted mb-0">
                    Thumbnail and shared gallery images. Variant-specific photos (e.g. the red
                    iPhone) are uploaded inside that variant's row above.
                </p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label class="form-label">
                            Thumbnail <span class="text-muted">(Optional)</span>
                        </label>

                        @if ($product && $product->thumbnail)
                            <div class="mb-2">
                                <p class="small text-muted">Current thumbnail (upload new to replace)</p>
                                <img src="{{ Storage::url($product->thumbnail) }}" alt="Current Thumbnail"
                                    class="img-thumbnail" style="max-width: 150px;" />
                            </div>
                        @endif

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

                    <div class="col-lg-8">
                        <label class="form-label">
                            Gallery Images <span class="text-muted">(Optional, max 10, shared across variants)</span>
                        </label>

                        @if ($product && $product->images->where('product_variant_id', null)->count() > 0)
                            <div class="mb-2">
                                <p class="small text-muted">Existing shared gallery images:</p>
                                <div class="row g-2" id="existingGalleryImages">
                                    @foreach ($product->images->where('product_variant_id', null) as $image)
                                        <div class="col-auto" data-image-id="{{ $image->id }}">
                                            <div class="position-relative">
                                                <img src="{{ Storage::url($image->image_path) }}" alt="Gallery Image"
                                                    class="img-thumbnail"
                                                    style="width: 100px; height: 100px; object-fit: cover;" />
                                                @if ($image->is_primary)
                                                    <span
                                                        class="badge bg-success position-absolute top-0 start-0 m-1">Primary</span>
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

    {{-- Right Column: Organization & SEO (Pricing card removed — now inside each variant row) --}}
    <div class="col-xxl-4">

        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Organization</h4>
                <p class="text-muted mb-0">Categorize and organize your product.</p>
            </div>
            <div class="card-body">

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

                <div class="mb-3">
                    <label class="form-label" for="is_active">
                        Status <span class="text-danger">*</span>
                    </label>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0 @error('is_active') is-invalid @enderror"
                            id="is_active" name="is_active" required>
                            <option value="">Select Status</option>
                            <option value="1"
                                {{ old('is_active', $product->is_active ?? '') == '1' ? 'selected' : '' }}>Active
                            </option>
                            <option value="0"
                                {{ old('is_active', $product->is_active ?? '') == '0' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="toggle-right"></i>
                    </div>
                    @error('is_active')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="is_featured">
                        Featured <span class="text-muted">(Optional)</span>
                    </label>
                    <div class="app-search">
                        <select
                            class="form-select form-control my-1 my-md-0 @error('is_featured') is-invalid @enderror"
                            id="is_featured" name="is_featured">
                            <option value="0"
                                {{ old('is_featured', $product->is_featured ?? '0') == '0' ? 'selected' : '' }}>Not
                                Featured</option>
                            <option value="1"
                                {{ old('is_featured', $product->is_featured ?? '') == '1' ? 'selected' : '' }}>Featured
                            </option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="star"></i>
                    </div>
                    @error('is_featured')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="mb-3">
                    <label class="form-label" for="related_products">
                        Related Products <span class="text-muted">(Optional)</span>
                    </label>
                    <select class="form-select @error('related_products') is-invalid @enderror" id="related_products"
                        name="related_products[]" data-search-url="{{ route('admin.ecommerce.product.search') }}"
                        multiple>
                        @if ($product && $product->relatedProducts->count() > 0)
                            @foreach ($product->relatedProducts as $related)
                                <option value="{{ $related->id }}" selected>{{ $related->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('related_products')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">SEO Meta</h4>
                <p class="text-muted mb-0">Optimize for search engines.</p>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="meta_title">Meta Title <span
                            class="text-muted">(Optional)</span></label>
                    <input class="form-control @error('meta.meta_title') is-invalid @enderror" id="meta_title"
                        name="meta[meta_title]" placeholder="SEO-friendly title" type="text" maxlength="255"
                        value="{{ old('meta.meta_title', $product->meta['meta_title'] ?? '') }}" />
                    @error('meta.meta_title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="meta_description">Meta Description <span
                            class="text-muted">(Optional)</span></label>
                    <textarea class="form-control @error('meta.meta_description') is-invalid @enderror" id="meta_description"
                        name="meta[meta_description]" placeholder="Brief description for search results" rows="3" maxlength="500">{{ old('meta.meta_description', $product->meta['meta_description'] ?? '') }}</textarea>
                    @error('meta.meta_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label" for="meta_keywords">Meta Keywords <span class="text-muted">(Optional,
                            comma-separated)</span></label>
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

{{-- Dropzone Preview Templates (unchanged) --}}
<div class="d-none" id="thumbnailPreviewTemplate">
    <div class="card mt-1 mb-0 border-dashed border">
        <div class="p-2">
            <div class="row align-items-center">
                <div class="col-auto"><img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail=""
                        src="#" /></div>
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
                <div class="col-auto"><img alt="" class="avatar-sm rounded bg-light" data-dz-thumbnail=""
                        src="#" /></div>
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

{{-- Template used by JS to clone a new blank variant row (index placeholder: __INDEX__) --}}
<template id="variantRowTemplate">
    @include('admin.ecommerce.product.variant-row', ['index' => '__INDEX__', 'variant' => null])
</template>
