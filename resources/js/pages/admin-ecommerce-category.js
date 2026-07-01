// resources/js/pages/admin-ecommerce-category.js

/**
 * Admin Category Management
 * Handles jQuery availability safely
 */

// ─────────────────────────────────────────────
// WAIT FOR JQUERY TO BE AVAILABLE
// ─────────────────────────────────────────────

function waitForJQuery(callback) {
    if (typeof window.jQuery !== 'undefined') {
        callback(window.jQuery);
    } else {
        // jQuery not ready yet, wait 50ms and try again
        setTimeout(function () {
            waitForJQuery(callback);
        }, 50);
    }
}

// ─────────────────────────────────────────────
// INIT EVERYTHING AFTER JQUERY IS READY
// ─────────────────────────────────────────────

waitForJQuery(function ($) {

    $(document).ready(function () {
        initDataTable();
        initAddEditModal();
        initDetailModal();
        initDeleteModal();
        initBulkDelete();
        initSlugGeneration();
        initImagePreview();
        initCheckboxes();
        initToggleStatusConfirm();
    });

    // ─────────────────────────────────────────────
    // DATATABLE
    // ─────────────────────────────────────────────

    let dataTable = null;

    const renderLucideIcons = () => {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
            if (iconSet) {
                lucide.createIcons({ icons: iconSet })
            }
        }
    }

    function initDataTable() {
        if (!document.getElementById('categoryTable')) return;

        dataTable = $('#categoryTable').DataTable({
            pageLength  : 10,
            lengthMenu  : [5, 10, 15, 25, 50],
            order       : [[6, 'asc']],
            columnDefs  : [
                { orderable: false,  targets: [0, 1, 8] },
                { searchable: false, targets: [0, 1, 8] },
            ],
            language: {
                search           : '',
                searchPlaceholder: 'Search categories...',
                lengthMenu       : 'Show _MENU_ entries',
                info             : 'Showing _START_ to _END_ of _TOTAL_ categories',
                // infoEmpty        : 'No categories found',
                emptyTable       : 'No categories available',
                paginate: {
                    first   : '«',
                    last    : '»',
                    next    : '›',
                    previous: '‹',
                },
            },
            dom:
                '<"row align-items-center mb-3"' +
                    '<"col-md-6"l>' +
                    '<"col-md-6"f>' +
                '>' +
                '<"row"<"col-12"tr>>' +
                '<"row align-items-center mt-3"' +
                    '<"col-md-5"i>' +
                    '<"col-md-7"p>' +
                '>',
            drawCallback: function () {
                renderLucideIcons();
                updateBulkDeleteVisibility();
            },
        });
    }

    // ─────────────────────────────────────────────
    // ADD / EDIT MODAL
    // ─────────────────────────────────────────────

    function initAddEditModal() {
        const modal     = document.getElementById('categoryModal');
        const addBtn    = document.getElementById('addCategoryBtn');
        const form      = document.getElementById('categoryForm');

        if (!modal) return;

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                resetModalForAdd();
            });
        }

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            if (trigger.dataset.mode === 'edit') {
                populateModalForEdit(trigger);
            } else {
                resetModalForAdd();
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            resetModalForAdd();
        });

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = document.getElementById('categorySubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1">' +
                        '</span>Saving...';
                }

                this.submit();
            });
        }
    }

    function resetModalForAdd() {
        const form      = document.getElementById('categoryForm');
        const title     = document.getElementById('categoryModalLabel');
        const submitBtn = document.getElementById('categorySubmitBtn');
        const storeUrl  = form?.dataset.storeUrl;

        if (!form) return;

        form.reset();
        if (storeUrl) form.action = storeUrl;

        // const methodField = document.getElementById('formMethod');
        // if (methodField) methodField.value = 'POST';

        if (title) title.textContent = 'Add New Category';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="plus" class="fs-sm me-1"></i> Add Category';
        }

        const slugField = document.getElementById('categorySlug');
        if (slugField) slugField.value = '';

        const previewContainer = document.getElementById('imagePreviewContainer');
        if (previewContainer) previewContainer.classList.add('d-none');

        const parentSelect = document.getElementById('categoryParent');
        if (parentSelect) parentSelect.disabled = false;

        renderLucideIcons();
    }

    function populateModalForEdit(trigger) {
        const {
            id,
            name,
            slug,
            description,
            parentId,
            sort,
            status,
            image,
            hasChildren,
            updateUrl,
        } = trigger.dataset;

        const form      = document.getElementById('categoryForm');
        const title     = document.getElementById('categoryModalLabel');
        const submitBtn = document.getElementById('categorySubmitBtn');

        if (!form) return;

        form.action = updateUrl;

        // const methodField = document.getElementById('formMethod');
        // if (methodField) methodField.value = 'PUT';

        setVal('categoryName',        name);
        setVal('categorySlug',        slug);
        setVal('categoryDescription', description || '');
        setVal('categorySortOrder',   sort || 0);
        setSelectVal('categoryParent', parentId || '');
        setSelectVal('categoryStatus', status);

        const parentSelect = document.getElementById('categoryParent');
        if (parentSelect) {
            parentSelect.disabled = parseInt(hasChildren) > 0;
        }

        if (image) {
            showImagePreview(image, 'Current Image');
        } else {
            const previewContainer = document.getElementById('imagePreviewContainer');
            if (previewContainer) previewContainer.classList.add('d-none');
        }

        const fileInput = document.getElementById('categoryImage');
        if (fileInput) fileInput.value = '';

        if (title) title.textContent = 'Edit Category';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="save" class="fs-sm me-1"></i> Update Category';
        }

        renderLucideIcons();
    }

    // ─────────────────────────────────────────────
    // DETAIL MODAL
    // ─────────────────────────────────────────────

    function initDetailModal() {
        const modal = document.getElementById('categoryDetailModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const {
                name, slug, description, type,
                parent, children, products,
                sort, status, image, created,
            } = trigger.dataset;

            setText('detailName',        name        || '—');
            setText('detailSlug',        slug        || '—');
            setText('detailType',        type        || '—');
            setText('detailParent',      parent      || '—');
            setText('detailChildren',    children    || '—');
            setText('detailProducts',    products    || '—');
            setText('detailSort',        sort        || '—');
            setText('detailDescription', description || '—');
            setText('detailCreated',     created     || '—');

            const statusEl = document.getElementById('detailStatus');
            if (statusEl) {
                const isActive = status === 'Active';
                statusEl.innerHTML =
                    `<span class="badge ${isActive ? 'bg-success' : 'bg-danger'}">
                        ${status}
                    </span>`;
            }

            const detailImage   = document.getElementById('detailImage');
            const detailNoImage = document.getElementById('detailNoImage');

            if (image) {
                if (detailImage)   { detailImage.src = image; detailImage.classList.remove('d-none'); }
                if (detailNoImage) { detailNoImage.classList.add('d-none'); }
            } else {
                if (detailImage)   { detailImage.classList.add('d-none'); }
                if (detailNoImage) { detailNoImage.classList.remove('d-none'); }
            }

            renderLucideIcons();
        });
    }

    // ─────────────────────────────────────────────
    // SINGLE DELETE MODAL
    // ─────────────────────────────────────────────

    function initDeleteModal() {
        const modal = document.getElementById('deleteCategoryModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger   = event.relatedTarget;
            if (!trigger) return;

            const name      = trigger.dataset.name;
            const children  = parseInt(trigger.dataset.children || 0);
            const deleteUrl = trigger.dataset.deleteUrl;

            const deleteForm = document.getElementById('deleteSingleForm');
            if (deleteForm) deleteForm.action = deleteUrl;

            const modalBody = document.getElementById('deleteModalBody');
            if (modalBody) {
                let html = `<p>Are you sure you want to delete 
                            <strong>${escapeHtml(name)}</strong>?</p>`;

                if (children > 0) {
                    html +=
                        `<div class="alert alert-warning mb-2">
                            <i data-lucide="triangle-alert" class="me-1"></i>
                            This category has
                            <strong>${children} sub-categor${children > 1 ? 'ies' : 'y'}</strong>
                            that will also be deleted.
                        </div>`;
                }

                html +=
                    `<p class="text-danger small mb-0">
                        <i data-lucide="info" class="me-1"></i>
                        This action cannot be undone.
                    </p>`;

                modalBody.innerHTML = html;
            }

            const confirmBtn  = document.getElementById('confirmDeleteBtn');
            const deleteForm2 = document.getElementById('deleteSingleForm');

            if (deleteForm2 && confirmBtn) {
                deleteForm2.addEventListener('submit', function () {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1">' +
                        '</span>Deleting...';
                }, { once: true });
            }

            renderLucideIcons();
        });
    }

    // ─────────────────────────────────────────────
    // BULK DELETE
    // ─────────────────────────────────────────────

    function initBulkDelete() {
        const bulkBtn   = document.getElementById('bulkDeleteBtn');
        const bulkModal = document.getElementById('bulkDeleteModal');

        if (!bulkBtn || !bulkModal) return;

        bulkBtn.addEventListener('click', function () {
            const selected = getSelectedIds();

            if (selected.length === 0) {
                alert('Please select at least one category.');
                return;
            }

            const msg = document.getElementById('bulkDeleteMessage');
            if (msg) {
                msg.textContent =
                    `Are you sure you want to delete ${selected.length} ` +
                    `selected categor${selected.length > 1 ? 'ies' : 'y'}?`;
            }

            const idsInput = document.getElementById('bulkDeleteIds');
            if (idsInput) idsInput.value = selected.join(',');

            const modal = new bootstrap.Modal(bulkModal);
            modal.show();
        });

        const bulkForm   = document.getElementById('bulkDeleteForm');
        const confirmBtn = document.getElementById('confirmBulkDeleteBtn');

        if (bulkForm && confirmBtn) {
            bulkForm.addEventListener('submit', function () {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1">' +
                    '</span>Deleting...';
            });
        }
    }

    // ─────────────────────────────────────────────
    // CHECKBOXES
    // ─────────────────────────────────────────────

    function initCheckboxes() {
        const selectAll = document.getElementById('selectAllCheckbox');
        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
            const visibleCheckboxes = getVisibleCheckboxes();
            visibleCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteVisibility();
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateSelectAllState();
                updateBulkDeleteVisibility();
            }
        });
    }

    function getVisibleCheckboxes() {
        if (!dataTable) {
            return Array.from(document.querySelectorAll('.row-checkbox'));
        }
        return Array.from(
            dataTable.rows({ page: 'current' }).nodes()
        ).map(row => row.querySelector('.row-checkbox')).filter(Boolean);
    }

    function getSelectedIds() {
        return Array.from(
            document.querySelectorAll('.row-checkbox:checked')
        ).map(cb => cb.value);
    }

    function updateSelectAllState() {
        const selectAll = document.getElementById('selectAllCheckbox');
        const visible   = getVisibleCheckboxes();
        const checked   = visible.filter(cb => cb.checked);

        if (selectAll) {
            selectAll.checked       = checked.length === visible.length && visible.length > 0;
            selectAll.indeterminate = checked.length > 0 && checked.length < visible.length;
        }
    }

    function updateBulkDeleteVisibility() {
        const bulkBtn  = document.getElementById('bulkDeleteBtn');
        const selected = getSelectedIds();

        if (bulkBtn) {
            if (selected.length > 0) {
                bulkBtn.classList.remove('d-none');
                bulkBtn.innerHTML =
                    `<i data-lucide="trash-2" class="fs-sm me-1"></i>` +
                    `Delete Selected (${selected.length})`;
                renderLucideIcons();
            } else {
                bulkBtn.classList.add('d-none');
            }
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS CONFIRM
    // ─────────────────────────────────────────────

    function initToggleStatusConfirm() {
        document.querySelectorAll('.toggle-status-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const btn    = this.querySelector('button[type="submit"]');
                const status = btn?.textContent?.trim();
                const action = status === 'Active' ? 'deactivate' : 'activate';

                if (!confirm(`Are you sure you want to ${action} this category?`)) {
                    e.preventDefault();
                }
            });
        });
    }

    // ─────────────────────────────────────────────
    // SLUG GENERATION
    // ─────────────────────────────────────────────

    function initSlugGeneration() {
        const nameInput = document.getElementById('categoryName');
        const slugInput = document.getElementById('categorySlug');

        if (!nameInput || !slugInput) return;

        nameInput.addEventListener('input', function () {
            const method = document.getElementById('formMethod')?.value;
            if (method === 'PUT') return;
            slugInput.value = generateSlug(this.value);
        });
    }

    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // ─────────────────────────────────────────────
    // IMAGE PREVIEW
    // ─────────────────────────────────────────────

    function initImagePreview() {
        const imageInput = document.getElementById('categoryImage');
        if (!imageInput) return;

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                alert('Invalid file type. Allowed: jpeg, jpg, png, webp');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File too large. Maximum size is 2MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                showImagePreview(e.target.result, 'New Image Preview');
            };
            reader.readAsDataURL(file);
        });
    }

    function showImagePreview(src, label = 'Image Preview') {
        const container = document.getElementById('imagePreviewContainer');
        const preview   = document.getElementById('imagePreview');
        const labelEl   = document.getElementById('imagePreviewLabel');

        if (preview)   preview.src         = src;
        if (labelEl)   labelEl.textContent = label;
        if (container) container.classList.remove('d-none');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    function setVal(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value ?? '';
    }

    function setSelectVal(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        Array.from(el.options).forEach(opt => {
            opt.selected = opt.value === String(value ?? '');
        });
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '—';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

}); // end waitForJQuery