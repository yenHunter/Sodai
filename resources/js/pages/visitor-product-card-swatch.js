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
    const img = card.querySelector('.ec-pro-image img.main-image')

    if (!card.dataset.defaultPrice && priceWrap) {
        card.dataset.defaultPrice = priceWrap.innerHTML
    }
    if (!card.dataset.defaultThumb && img) {
        card.dataset.defaultThumb = img.getAttribute('src')
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

    // Only reset if the swatch list was actually interacted with on this card
    const activeLi = card.querySelector('.ec-opt-swatch li.active')

    if (priceWrap && card.dataset.defaultPrice) {
        priceWrap.innerHTML = card.dataset.defaultPrice
    }

    if (img && card.dataset.defaultThumb) {
        img.setAttribute('src', card.dataset.defaultThumb)
    }

    // Also clear the theme's own "active"/"loaded" state so a fresh hover
    // re-triggers main.js's changeProductImg cleanly next time.
    if (activeLi) {
        card.querySelectorAll('.ec-opt-swatch li').forEach((el, i) => {
            el.classList.toggle('active', i === 0)
        })
    }
}