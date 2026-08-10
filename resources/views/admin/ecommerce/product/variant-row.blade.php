@php
    $v = $variant;
    $prefix = "variants[{$index}]";
    $optionValues = $v ? $v->optionValues->load('option') : collect();
@endphp
<div class="card border variant-row mb-3" data-variant-index="{{ $index }}" data-variant-id="{{ $v->id ?? '' }}">
    <div class="card-header d-flex align-items-center justify-content-between bg-light-subtle py-2">
        <div class="d-flex align-items-center gap-2">
            <i data-lucide="grip-vertical" class="text-muted" style="width:16px;height:16px;"></i>
            <span class="fw-semibold variant-label">
                {{ $optionValues->isNotEmpty() ? $optionValues->pluck('value')->implode(' / ') : 'Default (no options)' }}
            </span>
            @if ($v && $v->is_default)
                <span class="badge bg-primary-subtle text-primary variant-default-badge">Default</span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($v)
                <span class="text-muted small">SKU: {{ $v->sku }}</span>
            @else
                <span class="text-muted small">SKU auto-generated on save</span>
            @endif
            <button type="button" class="btn btn-sm btn-icon btn-default rounded-circle remove-variant-btn" title="Remove variant">
                <i data-lucide="x" style="width:14px;height:14px;"></i>
            </button>
        </div>
    </div>
    <div class="card-body">

        @if ($v)
            <input type="hidden" name="{{ $prefix }}[id]" value="{{ $v->id }}">
        @endif

        {{-- Option values for this variant (Color: Red, Size: M, ...) --}}
        <div class="row g-2 mb-3 variant-option-values">
            @forelse ($optionValues as $ovIndex => $ov)
                <div class="col-md-4 option-value-pair">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">{{ $ov->option->name }}</span>
                        <input type="hidden" name="{{ $prefix }}[option_values][{{ $ovIndex }}][option]" value="{{ $ov->option->name }}">
                        <input type="text" class="form-control" name="{{ $prefix }}[option_values][{{ $ovIndex }}][value]" value="{{ $ov->value }}">
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small fst-italic">No options — this is a simple, single-SKU variant.</div>
            @endforelse
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Price <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[price]" value="{{ old("variants.{$index}.price", $v->price ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Purchase Price</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[purchase_price]" value="{{ old("variants.{$index}.purchase_price", $v->purchase_price ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Discount Type</label>
                <select class="form-select form-select-sm" name="{{ $prefix }}[discount_type]">
                    <option value="">No Discount</option>
                    <option value="fixed" {{ old("variants.{$index}.discount_type", $v->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="percentage" {{ old("variants.{$index}.discount_type", $v->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Discount Value</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[discount_value]" value="{{ old("variants.{$index}.discount_value", $v->discount_value ?? '') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                <input type="number" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[stock_quantity]" value="{{ old("variants.{$index}.stock_quantity", $v->stock_quantity ?? 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Low Stock Threshold</label>
                <input type="number" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[low_stock_threshold]" value="{{ old("variants.{$index}.low_stock_threshold", $v->low_stock_threshold ?? 5) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Weight</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="{{ $prefix }}[weight]" value="{{ old("variants.{$index}.weight", $v->weight ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Weight Unit</label>
                <select class="form-select form-select-sm" name="{{ $prefix }}[weight_unit]">
                    <option value="">—</option>
                    @foreach (['kg', 'g', 'lb', 'oz'] as $unit)
                        <option value="{{ $unit }}" {{ old("variants.{$index}.weight_unit", $v->weight_unit ?? '') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="{{ $prefix }}[is_active]">
                    <option value="1" {{ old("variants.{$index}.is_active", $v->is_active ?? true) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !old("variants.{$index}.is_active", $v->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input variant-default-checkbox" type="checkbox"
                        name="{{ $prefix }}[is_default]" value="1"
                        {{ old("variants.{$index}.is_default", $v->is_default ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label small">Default variant (used when no option is pre-selected)</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Variant Thumbnail</label>
                <input type="file" class="form-control form-control-sm" name="{{ $prefix }}[thumbnail]" accept="image/*">
                @if ($v && $v->thumbnail)
                    <img src="{{ Storage::url($v->thumbnail) }}" class="img-thumbnail mt-1" style="max-width:60px;">
                @endif
            </div>
        </div>

        @if ($v && $v->images->isNotEmpty())
            <div class="mt-3">
                <label class="form-label mb-1 small fw-semibold">Variant Gallery</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($v->images as $img)
                        <div class="position-relative" data-image-id="{{ $img->id }}">
                            <img src="{{ Storage::url($img->image_path) }}" class="img-thumbnail"
                                style="width:70px;height:70px;object-fit:cover;">
                            <button type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-image-btn"
                                data-image-id="{{ $img->id }}" style="padding:2px 5px;">
                                <i data-lucide="x" style="width:10px;height:10px;"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="mt-2">
            <label class="form-label mb-1 small fw-semibold">Add Variant Images</label>
            <input type="file" class="form-control form-control-sm" name="{{ $prefix }}[images][]" accept="image/*" multiple>
        </div>
    </div>
</div>