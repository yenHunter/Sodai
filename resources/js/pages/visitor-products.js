import 'nouislider/dist/nouislider.css'
import noUiSlider from 'nouislider'

document.addEventListener('DOMContentLoaded', () => {
    initAutoSubmitFilters()
    initPriceSlider()
})

function initAutoSubmitFilters() {
    document.querySelectorAll('.filter-auto-submit').forEach(el => {
        el.addEventListener('change', () => document.getElementById('filterForm').submit())
    })
}

function initPriceSlider() {
    const slider = document.getElementById('ec-sliderPrice')
    if (!slider) return

    const min = parseFloat(slider.dataset.min)
    const max = parseFloat(slider.dataset.max)
    const step = parseFloat(slider.dataset.step) || 1
    const currentMin = parseFloat(slider.dataset.currentMin)
    const currentMax = parseFloat(slider.dataset.currentMax)

    const minDisplay = document.getElementById('priceMinDisplay')
    const maxDisplay = document.getElementById('priceMaxDisplay')
    const minInput = document.getElementById('priceMinInput')
    const maxInput = document.getElementById('priceMaxInput')

    noUiSlider.create(slider, {
        start: [currentMin, currentMax],
        connect: true,
        step: step,
        range: { min: min, max: max },
    })

    slider.noUiSlider.on('update', (values) => {
        const [lo, hi] = values.map(v => Math.round(parseFloat(v)))
        if (minDisplay) minDisplay.value = `$${lo}`
        if (maxDisplay) maxDisplay.value = `$${hi}`
        if (minInput) minInput.value = lo
        if (maxInput) maxInput.value = hi
    })
}