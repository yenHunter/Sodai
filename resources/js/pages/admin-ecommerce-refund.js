document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
    initModal()
    initCheckboxes()
    initBulkDelete()
})

function refreshIcons() {
    if (typeof lucide !== 'undefined') lucide.createIcons({ icons: lucide.icons })
}

function initAutoSubmitFilters() {
    const form = document.getElementById('filterForm')
    if (!form) return

    let t
    const searchInput = document.getElementById('searchInput')
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(t)
            t = setTimeout(() => form.submit(), 500)
        })
    }

    const statusFilter = document.getElementById('statusFilter')
    if (statusFilter) statusFilter.addEventListener('change', () => form.submit())
}

function initModal() {
    const modal = document.getElementById('refundModal')
    const addBtn = document.getElementById('addRefundBtn')
    const form = document.getElementById('refundForm')
    if (!modal) return

    if (addBtn) addBtn.addEventListener('click', resetForAdd)

    modal.addEventListener('show.bs.modal', function (e) {
        const trigger = e.relatedTarget
        if (trigger?.dataset.mode === 'edit') populateForEdit(trigger)
        else resetForAdd()
    })

    form.addEventListener('submit', function (e) {
        e.preventDefault()
        const btn = document.getElementById('refundSubmitBtn')
        btn.disabled = true
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
        this.submit()
    })
}

function resetForAdd() {
    const form = document.getElementById('refundForm')
    form.reset()
    form.action = form.dataset.storeUrl
    document.getElementById('refundModalLabel').textContent = 'New Refund Request'
    document.getElementById('refundSubmitBtn').innerHTML = '<i data-lucide="plus" class="fs-sm me-1"></i> Create Refund'
    refreshIcons()
}

function populateForEdit(trigger) {
    const { orderId, amount, reason, updateUrl } = trigger.dataset
    const form = document.getElementById('refundForm')

    form.action = updateUrl
    document.getElementById('refundOrderId').value = orderId || ''
    document.getElementById('refundAmount').value = amount || ''
    document.getElementById('refundReason').value = reason || ''

    document.getElementById('refundModalLabel').textContent = 'Edit Refund'
    document.getElementById('refundSubmitBtn').innerHTML = '<i data-lucide="save" class="fs-sm me-1"></i> Update Refund'
    refreshIcons()
}

function initCheckboxes() {
    const selectAll = document.getElementById('selectAllCheckbox')
    if (!selectAll) return

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => (cb.checked = this.checked))
        updateBulkBtn()
    })

    document.addEventListener('change', e => {
        if (e.target.classList.contains('row-checkbox')) updateBulkBtn()
    })
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value)
}

function updateBulkBtn() {
    const btn = document.getElementById('bulkDeleteBtn')
    if (!btn) return
    const selected = getSelectedIds()
    if (selected.length > 0) {
        btn.classList.remove('d-none')
        btn.innerHTML = `<i data-lucide="trash-2" class="fs-sm me-1"></i>Delete Selected (${selected.length})`
        refreshIcons()
    } else {
        btn.classList.add('d-none')
    }
}

function initBulkDelete() {
    const btn = document.getElementById('bulkDeleteBtn')
    if (!btn) return

    btn.addEventListener('click', () => {
        const selected = getSelectedIds()
        if (selected.length === 0) return
        if (!confirm(`Delete ${selected.length} selected refund(s)? Approved refunds will be skipped.`)) return

        document.getElementById('bulkDeleteIds').value = selected.join(',')
        document.getElementById('bulkDeleteForm').submit()
    })
}