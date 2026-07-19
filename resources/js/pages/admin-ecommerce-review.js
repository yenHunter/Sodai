document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
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

    ;['statusFilter', 'ratingFilter'].forEach(id => {
        const el = document.getElementById(id)
        if (el) el.addEventListener('change', () => form.submit())
    })
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
        if (!confirm(`Delete ${selected.length} selected review(s)?`)) return

        document.getElementById('bulkDeleteIds').value = selected.join(',')
        document.getElementById('bulkDeleteForm').submit()
    })
}