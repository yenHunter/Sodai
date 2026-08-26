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
    const toggle = document.getElementById('toggleCoupon')
    const box = document.getElementById('couponBox')
    const applyBtn = document.getElementById('applyCouponBtn')
    const input = document.getElementById('couponCodeInput')
    const hidden = document.getElementById('couponCodeHidden')
    const message = document.getElementById('couponMessage')
    const subtotalEl = document.getElementById('summarySubtotal')
    const totalEl = document.getElementById('summaryTotal')

    if (!toggle || !box) return

    toggle.addEventListener('click', () => {
        box.style.display = box.style.display === 'none' ? 'block' : 'none'
    })

    if (!applyBtn) return

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
            message.textContent = `Coupon "${data.code}" applied: -$${Number(data.discount_amount).toFixed(2)}`
            message.className = 'small mt-2 text-success'

            const subtotal = parseFloat(subtotalEl.textContent.replace('$', ''))
            const newTotal = Math.max(0, subtotal - Number(data.discount_amount))
            totalEl.textContent = `$${newTotal.toFixed(2)}`
        } catch (e) {
            message.textContent = 'Failed to apply coupon. Please try again.'
            message.className = 'small mt-2 text-danger'
        }
    })
}