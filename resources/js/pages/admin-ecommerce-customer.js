function waitForJQuery(cb) {
    if (typeof window.jQuery !== 'undefined') cb(window.jQuery)
    else setTimeout(() => waitForJQuery(cb), 50)
}

waitForJQuery(function ($) {
    $(document).ready(function () {
        initDataTable()
        initModal()
        initDelete()
        initBulkDelete()
        initCheckboxes()
        initToggleConfirm()
        initAvatarPreview()
    })

    let dataTable = null

    function refreshIcons() {
        if (typeof lucide !== 'undefined') lucide.createIcons({ icons: lucide.icons })
    }

    function initDataTable() {
        if (!document.getElementById('customerTable')) return
        dataTable = $('#customerTable').DataTable({
            pageLength: 10,
            columnDefs: [{ orderable: false, searchable: false, targets: [0, 1, 10] }],
            dom:
                '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                '<"row"<"col-12"tr>>' +
                '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            drawCallback: function () {
                refreshIcons()
                updateBulkDeleteVisibility()
            },
        })
    }

    function initModal() {
        const modal = document.getElementById('customerModal')
        const addBtn = document.getElementById('addCustomerBtn')
        const form = document.getElementById('customerForm')
        if (!modal) return

        if (addBtn) addBtn.addEventListener('click', resetForAdd)

        modal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            if (trigger?.dataset.mode === 'edit') populateForEdit(trigger)
            else resetForAdd()
        })

        form.addEventListener('submit', function (e) {
            e.preventDefault()
            const btn = document.getElementById('customerSubmitBtn')
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
            this.submit()
        })
    }

    function resetForAdd() {
        const form = document.getElementById('customerForm')
        form.reset()
        form.action = form.dataset.storeUrl
        document.getElementById('customerModalLabel').textContent = 'Add New Customer'
        document.getElementById('customerSubmitBtn').innerHTML = '<i data-lucide="plus" class="fs-sm me-1"></i> Add Customer'
        document.getElementById('newCustomerNotice').classList.remove('d-none')
        document.getElementById('avatarPreviewContainer').classList.add('d-none')
        refreshIcons()
    }

    function populateForEdit(trigger) {
        const { name, email, phone, status, image, updateUrl } = trigger.dataset
        const form = document.getElementById('customerForm')

        form.action = updateUrl
        document.getElementById('customerName').value = name || ''
        document.getElementById('customerEmail').value = email || ''
        document.getElementById('customerPhone').value = phone || ''
        document.getElementById('customerStatus').value = status || 'active'
        document.getElementById('newCustomerNotice').classList.add('d-none')

        const previewContainer = document.getElementById('avatarPreviewContainer')
        if (image) {
            document.getElementById('avatarPreview').src = image
            previewContainer.classList.remove('d-none')
        } else {
            previewContainer.classList.add('d-none')
        }

        document.getElementById('customerModalLabel').textContent = 'Edit Customer'
        document.getElementById('customerSubmitBtn').innerHTML = '<i data-lucide="save" class="fs-sm me-1"></i> Update Customer'
        refreshIcons()
    }

    function initAvatarPreview() {
        const input = document.getElementById('customerAvatar')
        if (!input) return

        input.addEventListener('change', function () {
            const file = this.files[0]
            if (!file) return

            const reader = new FileReader()
            reader.onload = e => {
                document.getElementById('avatarPreview').src = e.target.result
                document.getElementById('avatarPreviewContainer').classList.remove('d-none')
            }
            reader.readAsDataURL(file)
        })
    }

    function initDelete() {
        const modal = document.getElementById('deleteCustomerModal')
        if (!modal) return

        modal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            const orders = parseInt(trigger.dataset.orders || 0)

            document.getElementById('deleteSingleForm').action = trigger.dataset.deleteUrl

            let html = `<p>Are you sure you want to delete <strong>${trigger.dataset.name}</strong>?</p>`
            if (orders > 0) {
                html += `<div class="alert alert-warning mb-0"><i data-lucide="triangle-alert" class="me-1"></i>
                    This customer has <strong>${orders} order(s)</strong> and cannot be deleted.</div>`
            } else {
                html += `<p class="text-danger small mb-0">This action cannot be undone.</p>`
            }
            document.getElementById('deleteModalBody').innerHTML = html
            refreshIcons()
        })
    }

    function initBulkDelete() {
        const bulkBtn = document.getElementById('bulkDeleteBtn')
        const bulkModal = document.getElementById('bulkDeleteModal')
        if (!bulkBtn || !bulkModal) return

        bulkBtn.addEventListener('click', function () {
            const selected = getSelectedIds()
            if (selected.length === 0) { alert('Please select at least one customer.'); return }

            document.getElementById('bulkDeleteMessage').textContent =
                `Are you sure you want to delete ${selected.length} selected customer${selected.length > 1 ? 's' : ''}?`
            document.getElementById('bulkDeleteIds').value = selected.join(',')

            new bootstrap.Modal(bulkModal).show()
        })
    }

    function initCheckboxes() {
        const selectAll = document.getElementById('selectAllCheckbox')
        if (!selectAll) return

        selectAll.addEventListener('change', function () {
            getVisibleCheckboxes().forEach(cb => (cb.checked = this.checked))
            updateBulkDeleteVisibility()
        })

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('row-checkbox')) updateBulkDeleteVisibility()
        })
    }

    function getVisibleCheckboxes() {
        if (!dataTable) return Array.from(document.querySelectorAll('.row-checkbox'))
        return Array.from(dataTable.rows({ page: 'current' }).nodes())
            .map(row => row.querySelector('.row-checkbox'))
            .filter(Boolean)
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value)
    }

    function updateBulkDeleteVisibility() {
        const bulkBtn = document.getElementById('bulkDeleteBtn')
        const selected = getSelectedIds()
        if (!bulkBtn) return

        if (selected.length > 0) {
            bulkBtn.classList.remove('d-none')
            bulkBtn.innerHTML = `<i data-lucide="trash-2" class="fs-sm me-1"></i>Delete Selected (${selected.length})`
            refreshIcons()
        } else {
            bulkBtn.classList.add('d-none')
        }
    }

    function initToggleConfirm() {
        document.querySelectorAll('.toggle-status-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const status = this.querySelector('button')?.textContent?.trim()
                const action = status === 'Active' ? 'deactivate' : 'activate'
                if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this customer?`)) e.preventDefault()
            })
        })
    }
})