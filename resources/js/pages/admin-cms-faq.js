/**
 * Admin FAQ Management — categories (accordion) + FAQs (per-category tables)
 */

function waitForJQuery(callback) {
    if (typeof window.jQuery !== 'undefined') {
        callback(window.jQuery);
    } else {
        setTimeout(() => waitForJQuery(callback), 50);
    }
}

waitForJQuery(function ($) {

    $(document).ready(function () {
        initCategoryModal();
        initFaqModal();
        initDeleteCategoryModal();
        initDeleteFaqModal();
        initBulkDeleteFaq();
        initCheckboxes();
        initToggleStatusConfirm();
        renderLucideIcons();
    });

    const renderLucideIcons = () => {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            const iconSet = lucide.icons || (window.lucide && window.lucide.icons);
            if (iconSet) lucide.createIcons({ icons: iconSet });
        }
    };

    // ─────────────────────────────────────────────
    // CATEGORY MODAL
    // ─────────────────────────────────────────────

    function initCategoryModal() {
        const modal = document.getElementById('categoryModal');
        const addBtn = document.getElementById('addCategoryBtn');
        const form = document.getElementById('categoryForm');

        if (!modal) return;

        if (addBtn) {
            addBtn.addEventListener('click', resetCategoryModalForAdd);
        }

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            if (trigger.dataset.mode === 'edit') {
                populateCategoryModalForEdit(trigger);
            } else {
                resetCategoryModalForAdd();
            }
        });

        modal.addEventListener('hidden.bs.modal', resetCategoryModalForAdd);

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('categorySubmitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
                }
                this.submit();
            });
        }
    }

    function resetCategoryModalForAdd() {
        const form = document.getElementById('categoryForm');
        const title = document.getElementById('categoryModalLabel');
        const submitBtn = document.getElementById('categorySubmitBtn');
        const storeUrl = form?.dataset.storeUrl;

        if (!form) return;

        form.reset();
        if (storeUrl) form.action = storeUrl;
        if (title) title.textContent = 'Add Category';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="plus" class="fs-sm me-1"></i> Add Category';
        }
        setVal('categorySortOrder', 0);
        renderLucideIcons();
    }

    function populateCategoryModalForEdit(trigger) {
        const { name, sortOrder, status, updateUrl } = trigger.dataset;
        const form = document.getElementById('categoryForm');
        const title = document.getElementById('categoryModalLabel');
        const submitBtn = document.getElementById('categorySubmitBtn');

        if (!form) return;

        form.action = updateUrl;
        setVal('categoryName', name);
        setVal('categorySortOrder', sortOrder || 0);
        setSelectVal('categoryStatus', status);

        if (title) title.textContent = 'Edit Category';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="save" class="fs-sm me-1"></i> Update Category';
        }
        renderLucideIcons();
    }

    // ─────────────────────────────────────────────
    // FAQ MODAL
    // ─────────────────────────────────────────────

    function initFaqModal() {
        const modal = document.getElementById('faqModal');
        const addBtn = document.getElementById('addFaqBtn');
        const form = document.getElementById('faqForm');

        if (!modal) return;

        if (addBtn) {
            addBtn.addEventListener('click', resetFaqModalForAdd);
        }

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            if (trigger.dataset.mode === 'edit') {
                populateFaqModalForEdit(trigger);
            } else {
                resetFaqModalForAdd();
            }
        });

        modal.addEventListener('hidden.bs.modal', resetFaqModalForAdd);

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('faqSubmitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
                }
                this.submit();
            });
        }
    }

    function resetFaqModalForAdd() {
        const form = document.getElementById('faqForm');
        const title = document.getElementById('faqModalLabel');
        const submitBtn = document.getElementById('faqSubmitBtn');
        const storeUrl = form?.dataset.storeUrl;

        if (!form) return;

        form.reset();
        if (storeUrl) form.action = storeUrl;
        if (title) title.textContent = 'Add FAQ';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="plus" class="fs-sm me-1"></i> Add FAQ';
        }
        setVal('faqSortOrder', 0);
        renderLucideIcons();
    }

    function populateFaqModalForEdit(trigger) {
        const { categoryId, question, answer, sortOrder, status, updateUrl } = trigger.dataset;
        const form = document.getElementById('faqForm');
        const title = document.getElementById('faqModalLabel');
        const submitBtn = document.getElementById('faqSubmitBtn');

        if (!form) return;

        form.action = updateUrl;
        setSelectVal('faqCategoryId', categoryId);
        setVal('faqQuestion', question);
        setVal('faqAnswer', answer);
        setVal('faqSortOrder', sortOrder || 0);
        setSelectVal('faqStatus', status);

        if (title) title.textContent = 'Edit FAQ';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="save" class="fs-sm me-1"></i> Update FAQ';
        }
        renderLucideIcons();
    }

    // ─────────────────────────────────────────────
    // DELETE CATEGORY
    // ─────────────────────────────────────────────

    function initDeleteCategoryModal() {
        const modal = document.getElementById('deleteCategoryModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const { name, faqCount, deleteUrl } = trigger.dataset;
            const form = document.getElementById('deleteCategoryForm');
            if (form) form.action = deleteUrl;

            const body = document.getElementById('deleteCategoryModalBody');
            const count = parseInt(faqCount, 10) || 0;

            if (body) {
                if (count > 0) {
                    body.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-2"></i>
                            <strong>${escapeHtml(name)}</strong> still has ${count} FAQ(s).
                            You must move or delete them before removing this category.
                        </div>`;
                    const confirmBtn = document.getElementById('confirmDeleteCategoryBtn');
                    if (confirmBtn) confirmBtn.disabled = true;
                } else {
                    body.innerHTML = `<p class="mb-0">Are you sure you want to delete <strong>${escapeHtml(name)}</strong>?</p>`;
                    const confirmBtn = document.getElementById('confirmDeleteCategoryBtn');
                    if (confirmBtn) confirmBtn.disabled = false;
                }
            }

            renderLucideIcons();
        });

        const form = document.getElementById('deleteCategoryForm');
        const confirmBtn = document.getElementById('confirmDeleteCategoryBtn');
        if (form && confirmBtn) {
            form.addEventListener('submit', function () {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            });
        }
    }

    // ─────────────────────────────────────────────
    // DELETE SINGLE FAQ
    // ─────────────────────────────────────────────

    function initDeleteFaqModal() {
        const modal = document.getElementById('deleteFaqModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const { question, deleteUrl } = trigger.dataset;
            const form = document.getElementById('deleteFaqForm');
            if (form) form.action = deleteUrl;

            const body = document.getElementById('deleteFaqModalBody');
            if (body) {
                body.innerHTML = `<p class="mb-0">Are you sure you want to delete "<strong>${escapeHtml(question)}</strong>"?</p>`;
            }
        });

        const form = document.getElementById('deleteFaqForm');
        const confirmBtn = document.getElementById('confirmDeleteFaqBtn');
        if (form && confirmBtn) {
            form.addEventListener('submit', function () {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            });
        }
    }

    // ─────────────────────────────────────────────
    // BULK DELETE FAQ (per category panel)
    // ─────────────────────────────────────────────

    function initBulkDeleteFaq() {
        document.querySelectorAll('.bulk-delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const categoryId = this.dataset.category;
                const selected = getSelectedIdsForCategory(categoryId);

                if (selected.length === 0) {
                    alert('Please select at least one FAQ.');
                    return;
                }

                const msg = document.getElementById('bulkDeleteFaqMessage');
                if (msg) {
                    msg.textContent = `Are you sure you want to delete ${selected.length} selected FAQ${selected.length > 1 ? 's' : ''}?`;
                }

                const idsInput = document.getElementById('bulkDeleteFaqIds');
                if (idsInput) idsInput.value = selected.join(',');

                const bulkModal = document.getElementById('bulkDeleteFaqModal');
                new bootstrap.Modal(bulkModal).show();
            });
        });

        const bulkForm = document.getElementById('bulkDeleteFaqForm');
        const confirmBtn = document.getElementById('confirmBulkDeleteFaqBtn');
        if (bulkForm && confirmBtn) {
            bulkForm.addEventListener('submit', function () {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            });
        }
    }

    function getSelectedIdsForCategory(categoryId) {
        const panel = document.querySelector(`.accordion-item[data-category-id="${categoryId}"]`);
        if (!panel) return [];
        return Array.from(panel.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    }

    // ─────────────────────────────────────────────
    // CHECKBOXES (per category "select all" header)
    // ─────────────────────────────────────────────

    function initCheckboxes() {
        document.querySelectorAll('.row-checkbox-all').forEach(selectAll => {
            selectAll.addEventListener('change', function () {
                const categoryId = this.dataset.category;
                const panel = document.querySelector(`.accordion-item[data-category-id="${categoryId}"]`);
                if (!panel) return;

                panel.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkButtonVisibility(categoryId);
            });
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('row-checkbox')) {
                const panel = e.target.closest('.accordion-item');
                if (panel) updateBulkButtonVisibility(panel.dataset.categoryId);
            }
        });
    }

    function updateBulkButtonVisibility(categoryId) {
        const selected = getSelectedIdsForCategory(categoryId);
        const bulkBtn = document.querySelector(`.bulk-delete-btn[data-category="${categoryId}"]`);

        if (bulkBtn) {
            if (selected.length > 0) {
                bulkBtn.classList.remove('d-none');
                bulkBtn.innerHTML = `<i data-lucide="trash-2" class="fs-sm me-1"></i> Delete Selected (${selected.length})`;
                renderLucideIcons();
            } else {
                bulkBtn.classList.add('d-none');
            }
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS CONFIRM (category + FAQ share the same class)
    // ─────────────────────────────────────────────

    function initToggleStatusConfirm() {
        document.querySelectorAll('.toggle-status-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const btn = this.querySelector('button[type="submit"]');
                const status = btn?.textContent?.trim();
                const action = status === 'Active' ? 'deactivate' : 'activate';

                if (!confirm(`Are you sure you want to ${action} this?`)) {
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
        div.appendChild(document.createTextNode(text ?? ''));
        return div.innerHTML;
    }

});