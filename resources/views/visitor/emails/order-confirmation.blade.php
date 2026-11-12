<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .email-wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .email-header { background-color: #1a1a2e; padding: 30px; text-align: center; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .email-body { padding: 40px 30px; color: #333333; }
        .email-body p { line-height: 1.6; margin-bottom: 20px; }
        .order-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        .order-table th, .order-table td { padding: 10px 8px; border-bottom: 1px solid #eeeeee; text-align: left; }
        .order-table th { background: #f8f9fa; }
        .order-table td:last-child, .order-table th:last-child { text-align: right; }
        .totals { margin-left: auto; width: 260px; font-size: 14px; }
        .totals p { display: flex; justify-content: space-between; margin: 6px 0; }
        .totals .total { font-size: 18px; font-weight: bold; border-top: 2px solid #1a1a2e; padding-top: 8px; }
        .btn-track { display: inline-block; padding: 14px 32px; background-color: #1a1a2e; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; margin: 10px 0 20px; }
        .url-fallback { background: #f8f9fa; border-radius: 4px; padding: 12px; font-size: 12px; color: #6c757d; word-break: break-all; margin-top: 16px; }
        .email-footer { background: #f8f9fa; padding: 20px 30px; text-align: center; font-size: 12px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="email-body">
            <p>Hello, <strong>{{ $order->shipping_name }}</strong>!</p>
            <p>
                Thank you for your order. We have received order
                <strong>#{{ $order->order_number }}</strong> and it is now <strong>{{ ucfirst($order->status) }}</strong>.
            </p>

            <table class="order-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format((float) $item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <p><span>Subtotal</span><span>${{ number_format((float) $order->subtotal, 2) }}</span></p>
                @if ((float) $order->discount_amount > 0)
                    <p><span>Discount</span><span>-${{ number_format((float) $order->discount_amount, 2) }}</span></p>
                @endif
                <p><span>Shipping</span><span>${{ number_format((float) $order->shipping_charge, 2) }}</span></p>
                <p><span>Tax</span><span>${{ number_format((float) $order->tax_amount, 2) }}</span></p>
                <p class="total"><span>Total</span><span>${{ number_format((float) $order->total_amount, 2) }}</span></p>
            </div>

            <p style="margin-top: 30px;">
                You can follow your order's progress at any time. The button below opens the tracking page with
                your order number already filled in; enter the email address this message was sent to.
            </p>
            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="btn-track">Track My Order</a>
            </div>
            <p>If the button doesn't work, copy and paste this URL:</p>
            <div class="url-fallback">{{ $trackingUrl }}</div>
        </div>
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
