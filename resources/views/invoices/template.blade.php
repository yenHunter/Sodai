<h1>Invoice #{{ $order->invoice_number }}</h1>
<p>Customer: {{ $order->user->name }}</p>
<table width="100%" border="1">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->variant->product->name }} ({{ $item->variant->sku }})</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ $item->unit_price }}</td>
            <td>${{ $item->unit_price * $item->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3>Total Paid: ${{ $order->total_amount }}</h3>
