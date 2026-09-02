document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ec-product-inner').forEach((card) => {
        const img = card.querySelector('.ec-pro-image img.main-image')
        const priceWrap = card.querySelector('.product-card-price')
        if (!img || !priceWrap) return

        const defaultSrc = img.getAttribute('src')
        const defaultPriceHtml = priceWrap.innerHTML

        card.querySelectorAll('.product-swatch-option').forEach((option) => {
            option.addEventListener('mouseenter', () => {
                const thumb = option.dataset.thumb
                const price = option.dataset.price
                const oldPrice = option.dataset.oldPrice

                if (thumb) img.setAttribute('src', thumb)

                if (price) {
                    priceWrap.innerHTML = (oldPrice && oldPrice !== price)
                        ? `<span class="old-price">$${oldPrice}</span><span class="new-price">$${price}</span>`
                        : `<span class="new-price">$${price}</span>`
                }
            })

            option.addEventListener('click', (e) => {
                e.preventDefault()
                card.querySelectorAll('.product-swatch-option').forEach((o) => o.parentElement.classList.remove('active'))
                option.parentElement.classList.add('active')
            })
        })

        card.addEventListener('mouseleave', () => {
            img.setAttribute('src', defaultSrc)
            priceWrap.innerHTML = defaultPriceHtml
        })
    })
})