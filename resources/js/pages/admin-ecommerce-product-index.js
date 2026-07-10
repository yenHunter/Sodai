/**
 * Admin Product List Page
 * Server-side pagination with auto-submit filters
 */

document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
    initCheckboxes()
    initDeleteHandlers()
    refreshLucideIcons()
})

// ═══════════════════════════════════════════════
// AUTO-SUBMIT FILTERS
// ═══════════════════════════════════════════════

function initAutoSubmitFilters() {
    const form = document.getElementById('filterForm')
    const searchInput = document.getElementById('searchInput')
    const categoryFilter = document.getElementById('categoryFilter')
    const statusFilter = document.getElementById('statusFilter')

    if (!form) return

    // Search with debounce (500ms delay)
    let searchTimeout
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout)
            searchTimeout = setTimeout(() => form.submit(), 500)
        })
    }

    // Category filter - submit immediately
    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => form.submit())
    }

    // Status filter - submit immediately
    if (statusFilter) {
        statusFilter.addEventListener('change', () => form.submit())
    }
}

// ═══════════════════════════════════════════════
// CHECKBOX SELECTION
// ═══════════════════════════════════════════════

function initCheckboxes() {
    const selectAll = document.getElementById('selectAllCheckbox')
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn')

    if (!selectAll) return

    // Select/deselect all
    selectAll.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.row-checkbox')
        checkboxes.forEach(cb => cb.checked = this.checked)
        updateBulkDeleteButton()
    })

    // Individual checkbox change
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateSelectAllState()
            updateBulkDeleteButton()
        }
    })

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll('.row-checkbox')
        const checked = document.querySelectorAll('.row-checkbox:checked')

        if (selectAll) {
            selectAll.checked = checked.length === checkboxes.length && checkboxes.length > 0
            selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length
        }
    }

    function updateBulkDeleteButton() {
        const checked = document.querySelectorAll('.row-checkbox:checked')
        if (bulkDeleteBtn) {
            if (checked.length > 0) {
                bulkDeleteBtn.classList.remove('d-none')
                bulkDeleteBtn.textContent = `Delete Selected (${checked.length})`
            } else {
                bulkDeleteBtn.classList.add('d-none')
            }
        }
    }
}

// ═══════════════════════════════════════════════
// DELETE HANDLERS
// ═══════════════════════════════════════════════

function initDeleteHandlers() {
    initSingleDelete()
    initBulkDelete()
}

function initSingleDelete() {
    document.querySelectorAll('.delete-product-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId
            const productName = this.dataset.productName
            const deleteUrl = this.dataset.deleteUrl  // ✅ Read from data attribute

            const modal = new bootstrap.Modal(document.getElementById('deleteProductModal'))
            const modalBody = document.getElementById('deleteModalBody')
            const form = document.getElementById('deleteSingleForm')

            modalBody.innerHTML = `
                <p>Are you sure you want to delete <strong>${escapeHtml(productName)}</strong>?</p>
                <p class="text-danger small mb-0">
                    <i data-lucide="info" class="me-1"></i>
                    This action cannot be undone.
                </p>
            `

            form.action = deleteUrl  // ✅ Use route-generated URL

            refreshLucideIcons()
            modal.show()
        })
    })

    // Disable button on submit
    const form = document.getElementById('deleteSingleForm')
    const confirmBtn = document.getElementById('confirmDeleteBtn')
    if (form && confirmBtn) {
        form.addEventListener('submit', function () {
            confirmBtn.disabled = true
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...'
        })
    }
}

function initBulkDelete() {
    const bulkBtn = document.getElementById('bulkDeleteBtn')
    if (!bulkBtn) return

    bulkBtn.addEventListener('click', function () {
        const selected = getSelectedIds()
        if (selected.length === 0) {
            alert('Please select at least one product.')
            return
        }

        const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'))
        const message = document.getElementById('bulkDeleteMessage')
        const idsInput = document.getElementById('bulkDeleteIds')

        message.textContent = `Are you sure you want to delete ${selected.length} selected product${selected.length > 1 ? 's' : ''}?`
        idsInput.value = selected.join(',')

        refreshLucideIcons()
        modal.show()
    })

    // Disable button on submit
    const form = document.getElementById('bulkDeleteForm')
    const confirmBtn = document.getElementById('confirmBulkDeleteBtn')
    if (form && confirmBtn) {
        form.addEventListener('submit', function () {
            confirmBtn.disabled = true
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...'
        })
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => cb.value)
}

// ═══════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════

function escapeHtml(text) {
    const div = document.createElement('div')
    div.appendChild(document.createTextNode(text))
    return div.innerHTML
}

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) {
            lucide.createIcons({ icons: iconSet })
        }
    }
}