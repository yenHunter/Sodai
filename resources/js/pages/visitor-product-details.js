document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('variant-options')
    const matrixDataEl = document.getElementById('variant-matrix-data')
    const combinations = matrixDataEl ? JSON.parse(matrixDataEl.textContent) : {}

    const priceEl = document.getElementById('variant-price')
    const stockEl = document.getElementById('variant-stock-status')
    const skuEl = document.getElementById('variant-sku')
    const variantIdInput = document.getElementById('selected_variant_id')
    const addToCartBtn = document.getElementById('addToCartBtn')

    const selected = {}

    function resolveVariant() {
        const ids = Object.values(selected).map(Number).sort((a, b) => a - b)
        const variant = combinations[ids.join('-')]

        if (!variant) {
            if (stockEl) stockEl.textContent = 'Not Available'
            if (addToCartBtn) addToCartBtn.disabled = true
            return
        }

        if (priceEl) priceEl.textContent = '$' + Number(variant.final_price).toFixed(2)
        if (skuEl) skuEl.textContent = 'SKU#: ' + variant.sku
        if (variantIdInput) variantIdInput.value = variant.variant_id

        if (stockEl) {
            stockEl.textContent = !variant.is_in_stock
                ? 'Out of Stock'
                : (variant.is_low_stock ? 'Low Stock' : 'In Stock')
        }

        if (addToCartBtn) addToCartBtn.disabled = !variant.is_in_stock

        if (variant.thumbnail_url) {
            const mainImg = document.querySelector('.single-product-cover .single-slide img')
            if (mainImg) mainImg.src = variant.thumbnail_url
        }
    }

    if (root) {
        const defaultKey = root.dataset.defaultKey
        if (defaultKey) {
            defaultKey.split('-').filter(Boolean).forEach((id) => {
                const li = root.querySelector(`.variant-value-option[data-value-id="${id}"]`)
                if (!li) return
                li.classList.add('active')
                const group = li.closest('[data-option-name]')
                if (group) selected[group.dataset.optionName] = id
            })
        }

        root.querySelectorAll('.variant-value-option').forEach((li) => {
            li.addEventListener('click', function (e) {
                e.preventDefault()
                const group = li.closest('[data-option-name]')
                if (!group) return

                group.querySelectorAll('.variant-value-option').forEach((el) => el.classList.remove('active'))
                li.classList.add('active')
                selected[group.dataset.optionName] = li.dataset.valueId

                resolveVariant()
            })
        })
    }

    // Wishlist AJAX toggle
    document.querySelectorAll('.toggle-wishlist').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.preventDefault()
            const productId = btn.dataset.productId
            const token = document.querySelector('meta[name="csrf-token"]')?.content

            fetch(`/account/wishlist/${productId}/toggle`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
            })
                .then((res) => res.json())
                .then((data) => btn.classList.toggle('active', data.added))
                .catch(() => {})
        })
    })
})