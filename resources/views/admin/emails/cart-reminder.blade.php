<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You left something in your cart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; padding: 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .btn { display: inline-block; padding: 12px 28px; background: #1a1a2e; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header"><h1>Still thinking it over?</h1></div>
        <div class="body">
            <p>Hi {{ $customerName }},</p>
            <p>You left these items in your cart. They're still waiting for you!</p>
            <table>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['name'] }} × {{ $item['quantity'] }}</td>
                        <td align="right">${{ number_format($item['subtotal'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total</strong></td>
                    <td align="right"><strong>${{ number_format($total, 2) }}</strong></td>
                </tr>
            </table>
            <p style="text-align:center;">
                <a href="{{ $cartUrl }}" class="btn">Complete Your Order</a>
            </p>
        </div>
    </div>
</body>
</html>