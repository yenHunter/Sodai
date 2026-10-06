<!DOCTYPE html>
<html lang="en">
@php($periodNoun = strtolower($periodLabel) === 'monthly' ? 'month' : 'week')
<head>
    <meta charset="UTF-8">
    <title>{{ $periodLabel }} Sales Summary</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; padding: 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td, th { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .muted { color: #999; font-size: 12px; }
        .big { font-size: 26px; font-weight: bold; color: #1a1a2e; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ $periodLabel }} Sales Summary</h1>
            <p style="color:#bbb; margin:8px 0 0; font-size:13px;">
                {{ $periodStart->format('M j') }} – {{ $periodEnd->format('M j, Y') }}
            </p>
        </div>
        <div class="body">
            <p>Here's how the store performed last {{ $periodNoun }}.</p>

            <table>
                <tr>
                    <td><span class="big">${{ number_format($revenue, 2) }}</span><br><span class="muted">Revenue</span></td>
                    <td align="right">
                        <strong>{{ $orders }}</strong> order{{ $orders === 1 ? '' : 's' }}<br>
                        <span class="muted">Avg ${{ number_format($avgOrderValue, 2) }} / order</span>
                    </td>
                </tr>
                <tr>
                    <td><strong>{{ $itemsSold }}</strong> items sold</td>
                    <td align="right"><span class="muted">Discounts given: ${{ number_format($discounts, 2) }}</span></td>
                </tr>
            </table>

            @if ($topProducts->isNotEmpty())
                <p><strong>Top 5 products</strong></p>
                <table>
                    @forelse ($topProducts as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}. {{ $item->product_name }}@if($item->product_missing) <span class="muted">(deleted)</span>@endif</td>
                            <td align="right">{{ $item->quantity }} units · ${{ number_format($item->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="muted">No product sales last {{ $periodNoun }}.</td></tr>
                    @endforelse
                </table>
            @else
                <p class="muted">No product sales last {{ $periodNoun }}.</p>
            @endif

            @if ($lowStockCount > 0)
                <p>
                    ⚠ <strong>{{ $lowStockCount }}</strong> variant{{ $lowStockCount === 1 ? ' is' : 's are' }} currently
                    low or out of stock —
                    <a href="{{ route('admin.reports.low-stock') }}">review the restock list</a>.
                </p>
            @else
                <p>✅ No low-stock items right now.</p>
            @endif

            <p class="muted">
                You're receiving this because the {{ strtolower($periodLabel) }} sales summary is enabled in your store settings.
            </p>
        </div>
    </div>
</body>
</html>
