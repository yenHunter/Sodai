import { formatMoney, moneyOptions } from '../currency'

document.addEventListener('DOMContentLoaded', function () {
    initAddressToggle()
    initCouponBox()
})

function initAddressToggle() {
    const radios = document.querySelectorAll('input[name="address_id"]')
    const newAddressFields = document.getElementById('newAddressFields')
    if (!radios.length || !newAddressFields) return

    function sync() {
        const selected = document.querySelector('input[name="address_id"]:checked')
        const usingSavedAddress = selected && selected.value !== ''
        newAddressFields.style.display = usingSavedAddress ? 'none' : 'block'
    }

    radios.forEach((r) => r.addEventListener('change', sync))
    sync()
}

function initCouponBox() {
    const box = document.getElementById('couponBox')
    const applyBtn = document.getElementById('applyCouponBtn')
    const input = document.getElementById('couponCodeInput')
    const hidden = document.getElementById('couponCodeHidden')
    const message = document.getElementById('couponMessage')
    const subtotalEl = document.getElementById('summarySubtotal')
    const totalEl = document.getElementById('summaryTotal')

    if (!box || !applyBtn) return

    // The theme's main.js already slides this box with the `ec-checkout-coupan`
    // link; a second toggle handler here fights that animation and closes the
    // box again immediately after it opens.

    applyBtn.addEventListener('click', async () => {
        const code = input.value.trim()
        if (!code) return

        const token = document.querySelector('meta[name="csrf-token"]')?.content

        try {
            const res = await fetch('/checkout/apply-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ code }),
            })
            const data = await res.json()

            if (!data.success) {
                message.textContent = data.message || 'Invalid coupon.'
                message.className = 'small mt-2 text-danger'
                hidden.value = ''
                return
            }

            hidden.value = data.code
            const currency = moneyOptions(subtotalEl)
            message.textContent = `Coupon "${data.code}" applied: -${formatMoney(data.discount_amount, currency)}`
            message.className = 'small mt-2 text-success'

            const shipping = Number(document.getElementById('summaryShipping')?.dataset.amount || 0)
            const tax = Number(document.getElementById('summaryTax')?.dataset.amount || 0)
            const subtotal = Number(subtotalEl.dataset.amount || 0)
            const newTotal = Math.max(0, subtotal + shipping + tax - Number(data.discount_amount))

            totalEl.dataset.amount = newTotal
            totalEl.textContent = formatMoney(newTotal, currency)
        } catch (e) {
            message.textContent = 'Failed to apply coupon. Please try again.'
            message.className = 'small mt-2 text-danger'
        }
    })
}