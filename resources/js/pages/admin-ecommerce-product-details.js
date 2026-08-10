/**
 * Admin Product Details Page
 * Handles: status toggle confirm, delete confirm, per-variant quick stock update (AJAX)
 */

document.addEventListener('DOMContentLoaded', () => {
    refreshLucideIcons()
    initToggleStatusConfirm()
    initDeleteConfirm()
    initVariantStockUpdates()
})

// ═══════════════════════════════════════════════
// TOGGLE STATUS CONFIRM
// ═══════════════════════════════════════════════

function initToggleStatusConfirm() {
    const form = document.getElementById('toggleStatusForm')
    if (!form) return

    form.addEventListener('submit', function (e) {
        const activating = this.dataset.activating === '1'
        const action = activating ? 'activate' : 'deactivate'

        if (!confirm(`Are you sure you want to ${action} this product?`)) {
            e.preventDefault()
            return
        }

        const btn = this.querySelector('button[type="submit"]')
        if (btn) {
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Please wait...'
        }
    })
}

// ═══════════════════════════════════════════════
// DELETE CONFIRM
// ═══════════════════════════════════════════════

function initDeleteConfirm() {
    const form = document.getElementById('deleteProductForm')
    const confirmBtn = document.getElementById('confirmDeleteBtn')

    if (!form || !confirmBtn) return

    form.addEventListener('submit', function () {
        confirmBtn.disabled = true
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...'
    })
}

// ═══════════════════════════════════════════════
// PER-VARIANT QUICK STOCK UPDATE (AJAX)
// One row per <tr> in the Variants table, each with its own
// data-update-url pointing at /products/{product}/variants/{variant}/stock
// ═══════════════════════════════════════════════

function initVariantStockUpdates() {
    document.querySelectorAll('.variant-stock-update').forEach(wrapper => {
        const updateUrl = wrapper.dataset.updateUrl
        const input = wrapper.querySelector('.variant-stock-input')
        const btn = wrapper.querySelector('.variant-stock-btn')
        const csrfToken = document.querySelector('input[name="_token"]')?.value
            || document.querySelector('meta[name="csrf-token"]')?.content

        if (!updateUrl || !input || !btn) return

        btn.addEventListener('click', function () {
            const quantity = parseInt(input.value, 10)

            if (isNaN(quantity) || quantity < 0) {
                alert('Please enter a valid quantity.')
                return
            }

            btn.disabled = true
            const originalHtml = btn.innerHTML
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;"></span>'

            fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ stock_quantity: quantity }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateRowStatusBadge(wrapper, data)
                        updateTotalStockDisplay(data.product_total_stock)
                    } else {
                        alert(data.message || 'Failed to update stock.')
                    }
                })
                .catch(() => {
                    alert('An error occurred. Please try again.')
                })
                .finally(() => {
                    btn.disabled = false
                    btn.innerHTML = originalHtml
                    refreshLucideIcons()
                })
        })
    })
}

function updateRowStatusBadge(wrapper, data) {
    const row = wrapper.closest('tr')
    if (!row) return

    const badge = row.querySelector('td:nth-last-child(2) .badge')
    if (badge) {
        badge.classList.remove(
            'bg-success-subtle', 'text-success',
            'bg-warning-subtle', 'text-warning',
            'bg-danger-subtle', 'text-danger'
        )

        if (data.is_out_of_stock) {
            badge.classList.add('bg-danger-subtle', 'text-danger')
        } else if (data.is_low_stock) {
            badge.classList.add('bg-warning-subtle', 'text-warning')
        } else {
            badge.classList.add('bg-success-subtle', 'text-success')
        }

        badge.textContent = data.stock_status
    }
}

function updateTotalStockDisplay(totalStock) {
    if (typeof totalStock === 'undefined') return

    const el = document.getElementById('stockQuantityDisplay')
    if (el) el.textContent = totalStock
}

// ═══════════════════════════════════════════════
// LUCIDE ICONS REFRESH
// ═══════════════════════════════════════════════

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) lucide.createIcons({ icons: iconSet })
    }
}