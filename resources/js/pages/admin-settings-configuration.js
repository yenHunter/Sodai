/**
 * Admin Settings / Configuration pages
 * Handles: image previews (Design page), remove-checkbox disabling
 * the file input, and form submit loading states.
 */

document.addEventListener('DOMContentLoaded', () => {
    initImagePreviews();
    initRemoveCheckboxes();
    initFormSubmitStates();
    initOperationAreaControls();
    initShippingPreview();
    initMapEmbedPreview();
});

// ─────────────────────────────────────────────
// IMAGE PREVIEW (Design + Marketing pages)
// ─────────────────────────────────────────────

function initImagePreviews() {
    const keys = ['logo', 'logo_dark', 'favicon', 'login_bg', 'og_image'];

    keys.forEach(key => {
        const input = document.getElementById(key);
        if (!input) return;

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml', 'image/x-icon'];
            if (!allowed.includes(file.type)) {
                alert('Invalid file type selected.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById(`${key}Preview`);
                const icon = document.getElementById(`${key}PreviewIcon`);

                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                if (icon) icon.classList.add('d-none');

                const removeCheckbox = document.getElementById(`remove_${key}`);
                if (removeCheckbox) removeCheckbox.checked = false;
            };
            reader.readAsDataURL(file);
        });
    });
}


// ─────────────────────────────────────────────
// REMOVE-IMAGE CHECKBOXES
// Checking "remove" clears/disables the file input for that field
// so the two actions can't conflict.
// ─────────────────────────────────────────────

function initRemoveCheckboxes() {
    document.querySelectorAll('[id^="remove_"]').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const key = this.id.replace('remove_', '');
            const fileInput = document.getElementById(key);

            if (this.checked && fileInput) {
                fileInput.value = '';
            }
        });
    });
}

// ─────────────────────────────────────────────
// FORM SUBMIT LOADING STATE
// Supports both the fixed IDs from Part 1 and the generic
// .settings-form / .btn-save-settings classes used from Part 2 onward.
// ─────────────────────────────────────────────

function initFormSubmitStates() {
    ['companySubmitBtn', 'designSubmitBtn'].forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        bindSubmitState(btn);
    });

    document.querySelectorAll('.btn-save-settings').forEach(btn => {
        bindSubmitState(btn);
    });
}

function bindSubmitState(btn) {
    const form = btn.closest('form');
    if (!form || form.dataset.submitBound === '1') return;

    form.dataset.submitBound = '1';
    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    });
}

// ─────────────────────────────────────────────
// OPERATION AREA CHECKBOX GRID (Shipping page)
// Select All / Clear All + live search filter
// ─────────────────────────────────────────────

function initOperationAreaControls() {
    const grid = document.getElementById('districtGrid');
    if (!grid) return;

    const selectAllBtn = document.getElementById('selectAllAreas');
    const clearAllBtn = document.getElementById('clearAllAreas');
    const searchInput = document.getElementById('areaSearchInput');

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            grid.querySelectorAll('.area-checkbox').forEach(cb => (cb.checked = true));
            grid.querySelectorAll('.area-checkbox').forEach(cb => cb.dispatchEvent(new Event('change')));
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', () => {
            grid.querySelectorAll('.area-checkbox').forEach(cb => (cb.checked = false));
            grid.querySelectorAll('.area-checkbox').forEach(cb => cb.dispatchEvent(new Event('change')));
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();

            grid.querySelectorAll('.district-option').forEach(option => {
                const label = option.querySelector('label')?.textContent?.toLowerCase() || '';
                option.classList.toggle('d-none', term !== '' && !label.includes(term));
            });
        });
    }
}

// ─────────────────────────────────────────────
// SHIPPING CHARGE LIVE PREVIEW (Shipping page)
// Mirrors SettingService::resolveShippingCharge() on the client
// using whatever operation areas are currently checked.
// ─────────────────────────────────────────────

function initShippingPreview() {
    const cityInput = document.getElementById('previewCity');
    const subtotalInput = document.getElementById('previewSubtotal');
    const resultBox = document.getElementById('previewResult');

    if (!cityInput || !resultBox || typeof window.__shippingSettingsInitial === 'undefined') return;

    const getCheckedAreas = () =>
        Array.from(document.querySelectorAll('.area-checkbox:checked')).map(cb => cb.value.toLowerCase().trim());

    const isWithinArea = (city, areas) => {
        const c = city.toLowerCase().trim();
        return areas.some(area => c.includes(area) || area.includes(c));
    };

    const recalculate = () => {
        const initial = window.__shippingSettingsInitial;

        const insideCharge = parseFloat(document.getElementById('inside_area_charge')?.value) || initial.insideCharge;
        const outsideCharge = parseFloat(document.getElementById('outside_area_charge')?.value) || initial.outsideCharge;
        const freeEnabled = document.getElementById('enable_free_shipping')?.checked ?? initial.freeShippingEnabled;
        const freeThreshold = parseFloat(document.getElementById('free_shipping_threshold')?.value) || initial.freeShippingThreshold;

        const city = cityInput.value.trim();
        const subtotal = parseFloat(subtotalInput.value) || 0;
        const areas = getCheckedAreas();

        if (!city) {
            resultBox.className = 'alert alert-secondary mb-0';
            resultBox.textContent = 'Select your operation area(s) and enter a city to preview the charge.';
            return;
        }

        if (freeEnabled && freeThreshold > 0 && subtotal >= freeThreshold) {
            resultBox.className = 'alert alert-success mb-0';
            resultBox.innerHTML = `<strong>Free Shipping</strong> — subtotal meets the ৳${freeThreshold.toFixed(2)} threshold.`;
            return;
        }

        const withinArea = areas.length > 0 && isWithinArea(city, areas);
        const charge = withinArea ? insideCharge : outsideCharge;

        resultBox.className = 'alert alert-info mb-0';
        resultBox.innerHTML =
            `<strong>${withinArea ? 'Inside Operation Area' : 'Outside Operation Area'}</strong> — ` +
            `shipping charge: <strong>৳${charge.toFixed(2)}</strong>`;
    };

    document.addEventListener('change', e => {
        if (e.target.classList.contains('area-checkbox') || e.target.id === 'enable_free_shipping') {
            recalculate();
        }
    });

    [
        'previewCity', 'previewSubtotal', 'inside_area_charge', 'outside_area_charge', 'free_shipping_threshold',
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', recalculate);
    });
}

// ─────────────────────────────────────────────
// GOOGLE MAP EMBED LIVE PREVIEW (Company page)
// ─────────────────────────────────────────────

function initMapEmbedPreview() {
    const input = document.getElementById('map_embed_url');
    const wrapper = document.getElementById('mapPreviewWrapper');
    const frame = document.getElementById('mapPreviewFrame');

    if (!input || !wrapper || !frame) return;

    const debounced = debounce(() => {
        const url = input.value.trim();

        if (url.startsWith('https://www.google.com/maps/embed')) {
            frame.src = url;
            wrapper.classList.remove('d-none');
        } else {
            wrapper.classList.add('d-none');
        }
    }, 400);

    input.addEventListener('input', debounced);
}

function debounce(fn, wait) {
    let t;
    return function (...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), wait);
    };
}