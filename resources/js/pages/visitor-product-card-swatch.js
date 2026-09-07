document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('mouseenter', handleSwap, true)
    document.addEventListener('click', handleSwap, true)
    document.addEventListener('mouseleave', handleReset, true)
})

function handleSwap(e) {
    const li = e.target.closest && e.target.closest('.ec-opt-swatch li')
    if (!li) return

    const option = li.querySelector('.product-swatch-option')
    if (!option) return

    const card = li.closest('.ec-product-inner')
    if (!card) return

    const priceWrap = card.querySelector('.product-card-price')

    // Cache the default price once, from current DOM state — safe to do here
    // since nothing else touches price before our handler runs.
    if (!card.dataset.defaultPrice && priceWrap) {
        card.dataset.defaultPrice = priceWrap.innerHTML
    }

    if (priceWrap) {
        const price = option.dataset.price
        const oldPrice = option.dataset.oldPrice
        if (price) {
            priceWrap.innerHTML = (oldPrice && oldPrice !== price)
                ? `<span class="old-price">$${oldPrice}</span><span class="new-price">$${price}</span>`
                : `<span class="new-price">$${price}</span>`
        }
    }
}

function handleReset(e) {
    const card = e.target.closest && e.target.closest('.ec-product-inner')
    if (!card) return
    if (card.contains(e.relatedTarget)) return

    const priceWrap = card.querySelector('.product-card-price')
    const img = card.querySelector('.ec-pro-image img.main-image')
    const swatchList = card.querySelector('.ec-opt-swatch')

    if (priceWrap && card.dataset.defaultPrice) {
        priceWrap.innerHTML = card.dataset.defaultPrice
    }

    // Restore from the fixed data-default-thumb attribute rendered server-side
    // (the product's actual default variant thumbnail) rather than anything
    // captured at runtime — main.js's own image-swap handler fires on an
    // unrelated event (mouseover, bubbling) with no guaranteed order relative
    // to ours, so caching "whatever the image currently shows" is unreliable.
    if (img && swatchList && swatchList.dataset.defaultThumb) {
        img.setAttribute('src', swatchList.dataset.defaultThumb)
    }

    if (swatchList) {
        swatchList.querySelectorAll('li').forEach((el, i) => {
            el.classList.toggle('active', i === 0)
        })
    }
}