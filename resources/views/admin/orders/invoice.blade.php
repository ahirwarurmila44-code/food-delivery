<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Invoice</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f5f5f5; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Food Delivery</h2>
        <p><strong>Invoice</strong></p>
    </div>

    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Customer:</strong> {{ $order->customer->name }} ({{ $order->customer->email }})</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($order->items as $item)
                @php $sub = $item->price * $item->quantity; $total += $sub; @endphp
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ $item->price }}</td>
                    <td>₹{{ number_format($sub, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td><strong>₹{{ number_format($total, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <p>Thank you for your order!</p>
</body>
</html>
