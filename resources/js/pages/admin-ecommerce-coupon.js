/**
 * Admin Coupon Management
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
        initTypeToggle();
        initCodeUppercase();
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
        if (!document.getElementById('couponTable')) return;

        dataTable = $('#couponTable').DataTable({
            pageLength  : 10,
            lengthMenu  : [5, 10, 15, 25, 50],
            order       : [[1, 'asc']],
            columnDefs  : [
                { orderable: false,  targets: [0, 8] },
                { searchable: false, targets: [0, 8] },
            ],
            language: {
                search           : '',
                searchPlaceholder: 'Search coupons...',
                lengthMenu       : 'Show _MENU_ entries',
                info             : 'Showing _START_ to _END_ of _TOTAL_ coupons',
                emptyTable       : 'No coupons available',
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
    // TYPE TOGGLE (hide "max discount" for fixed type)
    // ─────────────────────────────────────────────

    function initTypeToggle() {
        const typeSelect = document.getElementById('couponType');
        if (!typeSelect) return;

        typeSelect.addEventListener('change', function () {
            toggleMaximumDiscountField(this.value);
        });
    }

    function toggleMaximumDiscountField(type) {
        const wrapper = document.getElementById('maximumDiscountWrapper');
        const input   = document.getElementById('couponMaximumDiscount');

        if (!wrapper) return;

        if (type === 'fixed') {
            wrapper.classList.add('d-none');
            if (input) input.value = '';
        } else {
            wrapper.classList.remove('d-none');
        }
    }

    // ─────────────────────────────────────────────
    // CODE UPPERCASE ENFORCEMENT
    // ─────────────────────────────────────────────

    function initCodeUppercase() {
        const codeInput = document.getElementById('couponCode');
        if (!codeInput) return;

        codeInput.addEventListener('input', function () {
            const cursorPos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(cursorPos, cursorPos);
        });
    }

    // ─────────────────────────────────────────────
    // ADD / EDIT MODAL
    // ─────────────────────────────────────────────

    function initAddEditModal() {
        const modal  = document.getElementById('couponModal');
        const addBtn = document.getElementById('addCouponBtn');
        const form   = document.getElementById('couponForm');

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

                const submitBtn = document.getElementById('couponSubmitBtn');
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
        const form      = document.getElementById('couponForm');
        const title     = document.getElementById('couponModalLabel');
        const submitBtn = document.getElementById('couponSubmitBtn');
        const storeUrl  = form?.dataset.storeUrl;

        if (!form) return;

        form.reset();
        if (storeUrl) form.action = storeUrl;

        if (title) title.textContent = 'Add New Coupon';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="plus" class="fs-sm me-1"></i> Add Coupon';
        }

        setVal('couponMinimumOrder', 0);
        setVal('couponUsagePerUser', 1);
        toggleMaximumDiscountField('');

        renderLucideIcons();
    }

    function populateModalForEdit(trigger) {
        const {
            code,
            type,
            value,
            minimumOrderAmount,
            maximumDiscount,
            usageLimit,
            usagePerUser,
            startsAt,
            expiresAt,
            status,
            updateUrl,
        } = trigger.dataset;

        const form      = document.getElementById('couponForm');
        const title     = document.getElementById('couponModalLabel');
        const submitBtn = document.getElementById('couponSubmitBtn');

        if (!form) return;

        form.action = updateUrl;

        setVal('couponCode',            code);
        setSelectVal('couponType',      type);
        setVal('couponValue',           value);
        setVal('couponMinimumOrder',    minimumOrderAmount || 0);
        setVal('couponMaximumDiscount', maximumDiscount || '');
        setVal('couponUsageLimit',      usageLimit || '');
        setVal('couponUsagePerUser',    usagePerUser || 1);
        setVal('couponStartsAt',        startsAt || '');
        setVal('couponExpiresAt',       expiresAt || '');
        setSelectVal('couponStatus',    status);

        toggleMaximumDiscountField(type);

        if (title) title.textContent = 'Edit Coupon';

        if (submitBtn) {
            submitBtn.disabled  = false;
            submitBtn.innerHTML =
                '<i data-lucide="save" class="fs-sm me-1"></i> Update Coupon';
        }

        renderLucideIcons();
    }

    // ─────────────────────────────────────────────
    // SINGLE DELETE MODAL
    // ─────────────────────────────────────────────

    function initDeleteModal() {
        const modal = document.getElementById('deleteCouponModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const code      = trigger.dataset.code;
            const used      = parseInt(trigger.dataset.used || 0);
            const deleteUrl = trigger.dataset.deleteUrl;

            const deleteForm = document.getElementById('deleteSingleForm');
            if (deleteForm) deleteForm.action = deleteUrl;

            const modalBody = document.getElementById('deleteModalBody');
            if (modalBody) {
                let html = `<p>Are you sure you want to delete
                            <strong>${escapeHtml(code)}</strong>?</p>`;

                if (used > 0) {
                    html +=
                        `<div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-1"></i>
                            This coupon has been used <strong>${used}</strong> time(s)
                            and cannot be deleted.
                        </div>`;
                } else {
                    html +=
                        `<p class="text-danger small mb-0">
                            <i data-lucide="info" class="me-1"></i>
                            This action cannot be undone.
                        </p>`;
                }

                modalBody.innerHTML = html;
            }

            const confirmBtn = document.getElementById('confirmDeleteBtn');
            if (confirmBtn) confirmBtn.disabled = used > 0;

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
                alert('Please select at least one coupon.');
                return;
            }

            const msg = document.getElementById('bulkDeleteMessage');
            if (msg) {
                msg.textContent =
                    `Are you sure you want to delete ${selected.length} ` +
                    `selected coupon${selected.length > 1 ? 's' : ''}?`;
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

                if (!confirm(`Are you sure you want to ${action} this coupon?`)) {
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