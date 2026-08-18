/**
 * Admin Settings / Configuration pages
 * Handles: image previews (Design page), remove-checkbox disabling
 * the file input, and form submit loading states.
 */

document.addEventListener('DOMContentLoaded', () => {
    initImagePreviews();
    initRemoveCheckboxes();
    initFormSubmitStates();
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