/**
 * Admin Offer Management
 * Handles jQuery availability safely
 */

function waitForJQuery(callback) {
    if (typeof window.jQuery !== 'undefined') {
        callback(window.jQuery);
    } else {
        setTimeout(function () {
            waitForJQuery(callback);
        }, 50);
    }
}

waitForJQuery(function ($) {

    $(document).ready(function () {
        initDataTable();
        initAddEditModal();
        initDeleteModal();
        initBulkDelete();
        initCheckboxes();
        initToggleStatusConfirm();
        initImagePreview();
    });

    let dataTable = null;

    const renderLucideIcons = () => {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
            if (iconSet) {
                lucide.createIcons({ icons: iconSet })
            }
        }
    }

    // ─────────────────────────────────────────────
    // DATATABLE
    // ─────────────────────────────────────────────

    function initDataTable() {
        if (!document.getElementById('offerTable')) return;

        dataTable = $('#offerTable').DataTable({
            pageLength  : 10,
            lengthMenu  : [5, 10, 15, 25, 50],
            order       : [[4, 'asc']],
            columnDefs  : [
                { orderable: false,  targets: [0, 1, 7] },
                { searchable: false, targets: [0, 1, 7] },
            ],
            language: {
                search           : '',
                searchPlaceholder: 'Search offers...',
                lengthMenu       : 'Show _MENU_ entries',
                info             : 'Showing _START_ to _END_ of _TOTAL_ offers',
                emptyTable       : 'No offers available',
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
        const modal  = document.getElementById('offerModal');
        const addBtn = document.getElementById('addOfferBtn');
        const form   = document.getElementById('offerForm');

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

                const submitBtn = document.getElementById('offerSubmitBtn');
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
        const form      = document.getElementById('offerForm');
        const title     = document.getElementById('offerModalLabel');
        const submitBtn = document.getElementById('offerSubmitBtn');
        const storeUrl  = form?.dataset.storeUrl;

        if (!form) return;

        form.reset();
        if (storeUrl) form.action = storeUrl;

        if (title) title.textContent = 'Add New Offer';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="plus" class="fs-sm me-1"></i> Add Offer';
        }

        setVal('offerSortOrder', 0);

        const previewContainer = document.getElementById('imagePreviewContainer');
        if (previewContainer) previewContainer.classList.add('d-none');

        const offerImage = document.getElementById('offerImage');
        if (offerImage) offerImage.required = true;

        renderLucideIcons();
    }

    function populateModalForEdit(trigger) {
        const {
            title,
            subtitle,
            description,
            buttonText,
            buttonUrl,
            categoryId,
            sortOrder,
            startsAt,
            expiresAt,
            status,
            image,
            updateUrl,
        } = trigger.dataset;

        const form      = document.getElementById('offerForm');
        const modalTitle = document.getElementById('offerModalLabel');
        const submitBtn = document.getElementById('offerSubmitBtn');

        if (!form) return;

        form.action = updateUrl;

        setVal('offerTitle',              title);
        setVal('offerSubtitle',           subtitle || '');
        setVal('offerDescription',        description);
        setVal('offerButtonText',         buttonText);
        setVal('offerButtonUrl',          buttonUrl);
        setSelectVal('offerCategory',     categoryId || '');
        setVal('offerSortOrder',          sortOrder || 0);
        setVal('offerStartsAt',           startsAt || '');
        setVal('offerExpiresAt',          expiresAt || '');
        setSelectVal('offerStatus',       status);

        const offerImageInput = document.getElementById('offerImage');
        if (offerImageInput) offerImageInput.required = false;

        if (image) {
            showImagePreview(image);
        }

        if (modalTitle) modalTitle.textContent = 'Edit Offer';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="save" class="fs-sm me-1"></i> Update Offer';
        }

        renderLucideIcons();
    }

    // ─────────────────────────────────────────────
    // IMAGE PREVIEW (new file selection)
    // ─────────────────────────────────────────────

    function initImagePreview() {
        const imageInput = document.getElementById('offerImage');
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

            if (file.size > 4 * 1024 * 1024) {
                alert('File too large. Maximum size is 4MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => showImagePreview(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    function showImagePreview(src) {
        const container = document.getElementById('imagePreviewContainer');
        const preview    = document.getElementById('imagePreview');

        if (preview) preview.src = src;
        if (container) container.classList.remove('d-none');
    }

    // ─────────────────────────────────────────────
    // SINGLE DELETE MODAL
    // ─────────────────────────────────────────────

    function initDeleteModal() {
        const modal = document.getElementById('deleteOfferModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const title     = trigger.dataset.title;
            const deleteUrl = trigger.dataset.deleteUrl;

            const deleteForm = document.getElementById('deleteSingleForm');
            if (deleteForm) deleteForm.action = deleteUrl;

            const modalBody = document.getElementById('deleteModalBody');
            if (modalBody) {
                modalBody.innerHTML = `
                    <p>Are you sure you want to delete <strong>${escapeHtml(title)}</strong>?</p>
                    <p class="text-danger small mb-0">
                        <i data-lucide="info" class="me-1"></i>
                        This action cannot be undone.
                    </p>`;
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
                alert('Please select at least one offer.');
                return;
            }

            const msg = document.getElementById('bulkDeleteMessage');
            if (msg) {
                msg.textContent =
                    `Are you sure you want to delete ${selected.length} ` +
                    `selected offer${selected.length > 1 ? 's' : ''}?`;
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

                if (!confirm(`Are you sure you want to ${action} this offer?`)) {
                    e.preventDefault();
                }
            });
        });
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

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

});