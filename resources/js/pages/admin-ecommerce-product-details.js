/**
 * Admin Product Details Page
 * Handles: status toggle confirm, delete confirm, quick stock update (AJAX)
 */

document.addEventListener('DOMContentLoaded', () => {
    refreshLucideIcons()
    initToggleStatusConfirm()
    initDeleteConfirm()
    initQuickStockUpdate()
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
// QUICK STOCK UPDATE (AJAX)
// ═══════════════════════════════════════════════

function initQuickStockUpdate() {
    const container = document.getElementById('quickStockUpdate')
    const input = document.getElementById('quickStockInput')
    const btn = document.getElementById('quickStockUpdateBtn')
    const status = document.getElementById('quickStockUpdateStatus')

    if (!container || !input || !btn) return

    const updateUrl = container.dataset.updateUrl
    const csrfToken = container.querySelector('input[name="_token"]')?.value

    btn.addEventListener('click', function () {
        const quantity = parseInt(input.value, 10)

        if (isNaN(quantity) || quantity < 0) {
            setStatus('Please enter a valid quantity.', 'text-danger')
            return
        }

        btn.disabled = true
        const originalText = btn.innerHTML
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'

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
                    setStatus('Stock updated.', 'text-success')
                    updateStockBadge(data)
                } else {
                    setStatus(data.message || 'Failed to update stock.', 'text-danger')
                }
            })
            .catch(() => {
                setStatus('An error occurred. Please try again.', 'text-danger')
            })
            .finally(() => {
                btn.disabled = false
                btn.innerHTML = originalText
            })
    })

    function setStatus(message, className) {
        if (!status) return
        status.textContent = message
        status.className = `small ${className}`
        setTimeout(() => {
            status.textContent = ''
        }, 3000)
    }

    function updateStockBadge(data) {
        const badge = document.getElementById('stockStatusBadge')
        const qtyDisplay = document.getElementById('stockQuantityDisplay')

        if (qtyDisplay) qtyDisplay.textContent = input.value

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
}

// ═══════════════════════════════════════════════
// LUCIDE ICONS REFRESH
// ═══════════════════════════════════════════════

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) {
            lucide.createIcons({ icons: iconSet })
        }
    }
}