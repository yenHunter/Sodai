/**
 * Client-side counterpart of App\Support\Currency::format().
 *
 * Views expose the store currency on the element that receives the formatted
 * value via `data-currency-symbol`, `data-currency-position` and
 * `data-currency-decimals`, so JS updates stay in sync with PHP formatting.
 */

const DEFAULTS = { symbol: '$', position: 'before', decimals: 2 }

export function moneyOptions(el) {
    const dataset = el?.dataset ?? {}
    const decimals = Number(dataset.currencyDecimals)

    return {
        symbol: dataset.currencySymbol || DEFAULTS.symbol,
        position: dataset.currencyPosition === 'after' ? 'after' : 'before',
        decimals: Number.isInteger(decimals) && decimals >= 0 && decimals <= 4 ? decimals : DEFAULTS.decimals,
    }
}

export function formatMoney(amount, options = {}) {
    const { symbol, position, decimals } = { ...DEFAULTS, ...options }
    const value = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(Number(amount) || 0)

    return position === 'after' ? `${value}${symbol}` : `${symbol}${value}`
}
