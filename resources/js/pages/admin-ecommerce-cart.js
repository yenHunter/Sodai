document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
    initCheckboxes()
    initBulkDelete()
    initViewModal()
    refreshIcons()
})

function refreshIcons() {
    if (typeof lucide !== 'undefined') lucide.createIcons({ icons: lucide.icons })
}

function initAutoSubmitFilters() {
    const form = document.getElementById('filterForm')
    const searchInput = document.getElementById('searchInput')
    if (!form || !searchInput) return

    let t
    searchInput.addEventListener('input', () => {
        clearTimeout(t)
        t = setTimeout(() => form.submit(), 500)
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
        if (!confirm(`Delete ${selected.length} selected cart(s)?`)) return

        document.getElementById('bulkDeleteIds').value = selected.join(',')
        document.getElementById('bulkDeleteForm').submit()
    })
}

function initViewModal() {
    document.querySelectorAll('.view-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            fetch(this.dataset.url)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalCustomerName').textContent = data.customer_name
                    document.getElementById('modalCustomerEmail').textContent = data.customer_email || ''
                    document.getElementById('modalUpdatedAt').textContent = data.updated_at

                    const body = document.getElementById('modalItemsBody')
                    body.innerHTML = data.items.map(item => `
                        <tr>
                            <td>${escapeHtml(item.product_name)}<br><small class="text-muted">${escapeHtml(item.product_sku)}</small></td>
                            <td>$${item.unit_price.toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td class="text-end">$${item.subtotal.toFixed(2)}</td>
                        </tr>
                    `).join('')

                    document.getElementById('modalTotal').textContent = `$${data.total.toFixed(2)}`

                    new bootstrap.Modal(document.getElementById('cartDetailModal')).show()
                })
                .catch(() => alert('Failed to load cart details.'))
        })
    })
}

function escapeHtml(text) {
    const div = document.createElement('div')
    div.appendChild(document.createTextNode(text ?? ''))
    return div.innerHTML
}