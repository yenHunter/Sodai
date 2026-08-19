/**
 * Admin Order POS Interface
 * Handles: customer select2, product select2, cart management, live totals
 */

import 'select2/dist/css/select2.min.css'
import $ from 'jquery'
import select2 from 'select2'

select2(window, $)

let cart = []

document.addEventListener('DOMContentLoaded', () => {
    initCustomerSelect2()
    initProductSelect2()
    seedExistingCart()
    initSummaryInputs()
    initQuickCustomerModal()
    initFormSubmit()
    initShippingAutoFill()
    initTaxAutoFill()
    initCouponApply()
    renderCart()
    refreshIcons()
})

// ─────────────────────────────────────────────
// CUSTOMER SELECT2
// ─────────────────────────────────────────────

function initCustomerSelect2() {
    const el = document.getElementById('customerSelect')
    if (!el) return

    const searchUrl = el.dataset.searchUrl
    const addressUrlTemplate = el.dataset.addressUrlTemplate

    $('#customerSelect').select2({
        placeholder: 'Search customer by name, email, or phone',
        allowClear: true,
        width: '100%',
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(c => ({ id: c.id, text: `${c.name} (${c.email})` })),
            }),
        },
        minimumInputLength: 2,
    })

    $('#customerSelect').on('select2:select', function (e) {
        const customerId = e.params.data.id
        const url = addressUrlTemplate.replace('__ID__', customerId)

        fetch(url)
            .then(r => r.json())
            .then(data => {
                setVal('shippingName', data.name)
                setVal('shippingEmail', data.email)
                if (data.phone) setVal('shippingPhone', data.phone)
                if (data.address) setVal('shippingAddress', data.address)
                if (data.city) setVal('shippingCity', data.city)
                if (data.state) setVal('shippingState', data.state)
                if (data.zip) setVal('shippingZip', data.zip)
                if (data.country) setVal('shippingCountry', data.country)
            })
            .catch(() => { })
    })
}

// ─────────────────────────────────────────────
// QUICK ADD CUSTOMER
// ─────────────────────────────────────────────

function initQuickCustomerModal() {
    const btn = document.getElementById('quickCustomerSubmitBtn')
    if (!btn) return

    btn.addEventListener('click', function () {
        const name = document.getElementById('quickCustomerName').value.trim()
        const email = document.getElementById('quickCustomerEmail').value.trim()
        const phone = document.getElementById('quickCustomerPhone').value.trim()
        const errorBox = document.getElementById('quickCustomerError')
        const token = document.querySelector('input[name="_token"]').value

        if (!name || !email) {
            errorBox.textContent = 'Name and email are required.'
            errorBox.classList.remove('d-none')
            return
        }

        btn.disabled = true

        fetch('/admin/ecommerce/orders/customers/quick-create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ name, email, phone }),
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const c = data.customer
                    const option = new Option(`${c.name} (${c.email})`, c.id, true, true)
                    $('#customerSelect').append(option).trigger('change')

                    setVal('shippingName', c.name)
                    setVal('shippingEmail', c.email)
                    if (c.phone) setVal('shippingPhone', c.phone)

                    errorBox.classList.add('d-none')
                    document.getElementById('quickCustomerName').value = ''
                    document.getElementById('quickCustomerEmail').value = ''
                    document.getElementById('quickCustomerPhone').value = ''

                    bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal'))?.hide()
                } else {
                    errorBox.textContent = data.message || 'Failed to create customer.'
                    errorBox.classList.remove('d-none')
                }
            })
            .catch(() => {
                errorBox.textContent = 'An error occurred. Please try again.'
                errorBox.classList.remove('d-none')
            })
            .finally(() => {
                btn.disabled = false
            })
    })
}

// ─────────────────────────────────────────────
// PRODUCT SELECT2
// ─────────────────────────────────────────────

function initProductSelect2() {
    const el = document.getElementById('productSearchSelect')
    if (!el) return

    const searchUrl = el.dataset.searchUrl

    $('#productSearchSelect').select2({
        placeholder: 'Search product by name or SKU',
        width: '100%',
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(p => ({
                    id: p.id,
                    text: `${p.name} (${p.sku}) — $${p.price.toFixed(2)} — Stock: ${p.stock_quantity}`,
                    raw: p,
                })),
            }),
        },
        minimumInputLength: 2,
    })

    $('#productSearchSelect').on('select2:select', function (e) {
        addToCart(e.params.data.raw)
        $(this).val(null).trigger('change')
    })
}

// ─────────────────────────────────────────────
// CART
// ─────────────────────────────────────────────

function seedExistingCart() {
    if (Array.isArray(window.__existingCartItems)) {
        cart = window.__existingCartItems.map(i => ({ ...i }))
    }
}

function addToCart(product) {
    const existing = cart.find(i => i.product_id === product.id)

    if (existing) {
        if (existing.quantity + 1 > product.stock_quantity) {
            alert(`Only ${product.stock_quantity} unit(s) of "${product.name}" available.`)
            return
        }
        existing.quantity += 1
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            sku: product.sku,
            thumbnail_url: product.thumbnail_url,
            price: product.price,
            quantity: 1,
            stock_quantity: product.stock_quantity,
        })
    }

    renderCart()
}

function updateQuantity(productId, quantity) {
    const item = cart.find(i => i.product_id === productId)
    if (!item) return

    quantity = Math.max(1, parseInt(quantity, 10) || 1)

    if (quantity > item.stock_quantity) {
        alert(`Only ${item.stock_quantity} unit(s) available.`)
        quantity = item.stock_quantity
    }

    item.quantity = quantity
    renderCart()
}

function removeFromCart(productId) {
    cart = cart.filter(i => i.product_id !== productId)
    renderCart()
}

function renderCart() {
    const body = document.getElementById('cartBody')
    if (!body) return

    if (cart.length === 0) {
        body.innerHTML = `
            <tr id="cartEmptyRow">
                <td colspan="5" class="text-center text-muted py-4">Cart is empty. Search a product above to add it.</td>
            </tr>`
    } else {
        body.innerHTML = cart.map(item => `
            <tr data-product-id="${item.product_id}">
                <td>
                    <div class="d-flex align-items-center">
                        ${item.thumbnail_url ? `<img src="${item.thumbnail_url}" class="avatar-sm rounded me-2" alt="">` : ''}
                        <div>
                            <h5 class="mb-0 fs-sm">${escapeHtml(item.name)}</h5>
                            <small class="text-muted">${escapeHtml(item.sku)}</small>
                        </div>
                    </div>
                </td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm cart-qty-input" min="1"
                        max="${item.stock_quantity}" value="${item.quantity}" data-product-id="${item.product_id}">
                </td>
                <td class="text-end fw-semibold cart-line-total">$${(item.price * item.quantity).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-icon btn-default rounded-circle cart-remove-btn" data-product-id="${item.product_id}">
                        <i data-lucide="x" style="width:14px;height:14px"></i>
                    </button>
                </td>
            </tr>
        `).join('')

        body.querySelectorAll('.cart-qty-input').forEach(input => {
            input.addEventListener('change', function () {
                updateQuantity(parseInt(this.dataset.productId, 10), this.value)
            })
        })

        body.querySelectorAll('.cart-remove-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                removeFromCart(parseInt(this.dataset.productId, 10))
            })
        })
    }

    refreshIcons()
    recalculateSummary()
    if (typeof window.__fetchTaxPreview === 'function') window.__fetchTaxPreview()
}

// ─────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────

function initSummaryInputs() {
    ;['discountAmount', 'shippingCharge', 'taxAmount'].forEach(id => {
        const el = document.getElementById(id)
        if (el) el.addEventListener('input', recalculateSummary)
    })
}

function recalculateSummary() {
    const subtotal = cart.reduce((sum, i) => sum + i.price * i.quantity, 0)
    const discount = parseFloat(document.getElementById('discountAmount')?.value) || 0
    const shipping = parseFloat(document.getElementById('shippingCharge')?.value) || 0
    const tax = parseFloat(document.getElementById('taxAmount')?.value) || 0

    const total = Math.max(0, subtotal - discount + shipping + tax)

    setText('summarySubtotal', `$${subtotal.toFixed(2)}`)
    setText('summaryTotal', `$${total.toFixed(2)}`)
}

// ─────────────────────────────────────────────
// SHIPPING AUTO-FILL (from Shipping settings)
// Recalculates whenever the city field changes or the cart total changes,
// but only overwrites the shipping field if the admin hasn't manually
// typed a value themselves (tracked via a "touched" flag).
// ─────────────────────────────────────────────

let shippingManuallyEdited = false
let taxManuallyEdited = false

function initShippingAutoFill() {
    const cityInput = document.getElementById('shippingCity')
    const shippingInput = document.getElementById('shippingCharge')
    const previewUrl = document.getElementById('previewShippingUrl')?.value

    if (!cityInput || !shippingInput || !previewUrl) return

    shippingInput.addEventListener('input', () => { shippingManuallyEdited = true })

    const fetchShippingPreview = debounce(() => {
        const city = cityInput.value.trim()
        const subtotal = getCartSubtotal()

        const url = new URL(previewUrl, window.location.origin)
        url.searchParams.set('city', city)
        url.searchParams.set('subtotal', subtotal)

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('shippingAreaBadge')
                if (badge) {
                    badge.textContent = city ? (data.within_area ? 'In Operation Area' : 'Outside Operation Area') : ''
                    badge.className = `badge ${data.within_area ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}`
                }

                if (!shippingManuallyEdited) {
                    shippingInput.value = Number(data.shipping_charge).toFixed(2)
                    recalculateSummary()
                }
            })
            .catch(() => { })
    }, 400)

    cityInput.addEventListener('input', fetchShippingPreview)

    // Initial fetch on load (edit page pre-fills city).
    if (cityInput.value.trim()) fetchShippingPreview()
}

// ─────────────────────────────────────────────
// TAX AUTO-FILL (from Tax settings)
// ─────────────────────────────────────────────

function initTaxAutoFill() {
    const taxInput = document.getElementById('taxAmount')
    const previewUrl = document.getElementById('previewTaxUrl')?.value

    if (!taxInput || !previewUrl) return

    taxInput.addEventListener('input', () => { taxManuallyEdited = true })

    window.__fetchTaxPreview = debounce(() => {
        const subtotal = getCartSubtotal()

        const url = new URL(previewUrl, window.location.origin)
        url.searchParams.set('subtotal', subtotal)

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const hint = document.getElementById('taxLabelHint')
                if (hint) {
                    hint.textContent = data.tax_enabled ? `(${data.tax_label} @ ${data.tax_rate}%)` : '(tax disabled)'
                }

                if (!taxManuallyEdited) {
                    taxInput.value = Number(data.tax_amount).toFixed(2)
                    recalculateSummary()
                }
            })
            .catch(() => { })
    }, 400)

    window.__fetchTaxPreview()
}

// ─────────────────────────────────────────────
// COUPON APPLY (AJAX validation)
// ─────────────────────────────────────────────

function initCouponApply() {
    const applyBtn = document.getElementById('applyCouponBtn')
    const codeInput = document.getElementById('couponCodeInput')
    const discountInput = document.getElementById('discountAmount')
    const feedback = document.getElementById('couponFeedback')
    const customerSelect = document.getElementById('customerSelect')
    const orderIdField = document.getElementById('orderIdField')

    if (!applyBtn || !codeInput) return

    codeInput.addEventListener('input', function () {
        const pos = this.selectionStart
        this.value = this.value.toUpperCase()
        this.setSelectionRange(pos, pos)
    })

    applyBtn.addEventListener('click', function () {
        const code = codeInput.value.trim()
        const userId = $('#customerSelect').val()
        const subtotal = getCartSubtotal()
        const token = document.querySelector('input[name="_token"]').value

        if (!code) {
            showCouponFeedback('Enter a coupon code first.', 'danger')
            return
        }
        if (!userId) {
            showCouponFeedback('Select a customer before applying a coupon.', 'danger')
            return
        }
        if (subtotal <= 0) {
            showCouponFeedback('Add products to the cart first.', 'danger')
            return
        }

        applyBtn.disabled = true
        applyBtn.textContent = 'Checking...'

        fetch(applyBtn.dataset.applyUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                code,
                user_id: userId,
                subtotal,
                order_id: orderIdField?.value || null,
            }),
        })
            .then(async r => ({ ok: r.ok, data: await r.json() }))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    showCouponFeedback(data.message || 'Invalid coupon.', 'danger')
                    return
                }

                discountInput.value = Number(data.discount_amount).toFixed(2)
                showCouponFeedback(`Applied "${data.code}" — ${data.value_label} off (−$${Number(data.discount_amount).toFixed(2)}).`, 'success')
                recalculateSummary()
            })
            .catch(() => showCouponFeedback('Something went wrong validating the coupon.', 'danger'))
            .finally(() => {
                applyBtn.disabled = false
                applyBtn.textContent = 'Apply'
            })
    })
}

function showCouponFeedback(message, type) {
    const feedback = document.getElementById('couponFeedback')
    if (!feedback) return
    feedback.textContent = message
    feedback.className = `small mt-1 text-${type}`
}

// ─────────────────────────────────────────────
// SHARED HELPERS
// ─────────────────────────────────────────────

function getCartSubtotal() {
    return cart.reduce((sum, i) => sum + i.price * i.quantity, 0)
}

function debounce(fn, wait) {
    let t
    return function (...args) {
        clearTimeout(t)
        t = setTimeout(() => fn.apply(this, args), wait)
    }
}

// ─────────────────────────────────────────────
// FORM SUBMIT
// ─────────────────────────────────────────────

function initFormSubmit() {
    const form = document.getElementById('orderForm')
    if (!form) return

    form.addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault()
            alert('Please add at least one product to the order.')
            return
        }

        const container = document.getElementById('cartItemsInputs')
        container.innerHTML = ''

        cart.forEach((item, index) => {
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `)
        })

        const btn = document.getElementById('submitOrderBtn')
        if (btn) {
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
        }
    })
}

// ─────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────

function setVal(id, value) {
    const el = document.getElementById(id)
    if (el) el.value = value ?? ''
}

function setText(id, value) {
    const el = document.getElementById(id)
    if (el) el.textContent = value
}

function escapeHtml(text) {
    const div = document.createElement('div')
    div.appendChild(document.createTextNode(text ?? ''))
    return div.innerHTML
}

function refreshIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) lucide.createIcons({ icons: iconSet })
    }
}