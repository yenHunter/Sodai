/**
 * Admin Order Index Page
 * Auto-submit filters + status change confirm
 */

document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
    initStatusChangeConfirm()
})

function initAutoSubmitFilters() {
    const form = document.getElementById('filterForm')
    if (!form) return

    let searchTimeout
    const searchInput = document.getElementById('searchInput')
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout)
            searchTimeout = setTimeout(() => form.submit(), 500)
        })
    }

    ;['statusFilter', 'dateFromFilter', 'dateToFilter'].forEach(id => {
        const el = document.getElementById(id)
        if (el) el.addEventListener('change', () => form.submit())
    })
}

function initStatusChangeConfirm() {
    document.querySelectorAll('.status-update-form select[name="status"]').forEach(select => {
        select.addEventListener('change', function () {
            if (this.value === 'cancelled') {
                if (!confirm('Cancelling will restore stock for all items in this order. Continue?')) {
                    return
                }
            }
            this.closest('form').submit()
        })
    })
}