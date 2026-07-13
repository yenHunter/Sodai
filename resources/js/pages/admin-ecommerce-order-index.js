/**
 * Admin Order Index Page
 * Auto-submit filters, daterangepicker, status change confirm
 */

import 'daterangepicker/daterangepicker.css'
import $ from 'jquery'
import 'daterangepicker'

document.addEventListener('DOMContentLoaded', () => {
    initDateRangePicker()
    initAutoSubmitFilters()
    initStatusChangeConfirm()
})

// ═══════════════════════════════════════════════
// DATE RANGE PICKER
// ═══════════════════════════════════════════════

function initDateRangePicker() {
    const input = document.getElementById('dateRangeFilter')
    if (!input) return

    const fromInput = document.getElementById('dateFromInput')
    const toInput = document.getElementById('dateToInput')
    const form = document.getElementById('filterForm')

    const existingFrom = fromInput?.value
    const existingTo = toInput?.value

    const options = {
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear',
        },
        ranges: {
            'Today': [window.moment(), window.moment()],
            'Yesterday': [window.moment().subtract(1, 'days'), window.moment().subtract(1, 'days')],
            'Last 7 Days': [window.moment().subtract(6, 'days'), window.moment()],
            'Last 30 Days': [window.moment().subtract(29, 'days'), window.moment()],
            'This Month': [window.moment().startOf('month'), window.moment().endOf('month')],
            'Last Month': [
                window.moment().subtract(1, 'month').startOf('month'),
                window.moment().subtract(1, 'month').endOf('month'),
            ],
        },
    }

    $(input).daterangepicker(options)

    if (existingFrom && existingTo) {
        $(input).data('daterangepicker').setStartDate(existingFrom)
        $(input).data('daterangepicker').setEndDate(existingTo)
        input.value = `${existingFrom} to ${existingTo}`
    }

    $(input).on('apply.daterangepicker', function (ev, picker) {
        const from = picker.startDate.format('YYYY-MM-DD')
        const to = picker.endDate.format('YYYY-MM-DD')

        input.value = `${from} to ${to}`
        if (fromInput) fromInput.value = from
        if (toInput) toInput.value = to

        form.submit()
    })

    $(input).on('cancel.daterangepicker', function () {
        input.value = ''
        if (fromInput) fromInput.value = ''
        if (toInput) toInput.value = ''
        form.submit()
    })
}

// ═══════════════════════════════════════════════
// AUTO-SUBMIT FILTERS
// ═══════════════════════════════════════════════

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

    const statusFilter = document.getElementById('statusFilter')
    if (statusFilter) {
        statusFilter.addEventListener('change', () => form.submit())
    }
}

// ═══════════════════════════════════════════════
// STATUS CHANGE CONFIRM
// ═══════════════════════════════════════════════

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