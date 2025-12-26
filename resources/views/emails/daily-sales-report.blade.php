<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #111827;
        }
        tr:hover {
            background-color: #f9fafb;
        }
        .total-row {
            font-weight: 600;
            background-color: #f3f4f6;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 4px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Sales Report</h1>
        <p style="margin: 0;">Date: {{ $date }}</p>
    </div>

    <div class="content">
        <div style="margin-bottom: 20px;">
            <p>Hello,</p>
            <p>Please find below the daily sales report for {{ $date }}. This report contains a summary of all products sold today.</p>
        </div>

        @if(count($salesData) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                    @endphp
                    @foreach($salesData as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                        <td><strong>${{ number_format($grandTotal, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-item">
                    <span><strong>Total Products Sold:</strong></span>
                    <span><strong>{{ count($salesData) }}</strong></span>
                </div>
                <div class="summary-item">
                    <span><strong>Total Quantity:</strong></span>
                    <span><strong>{{ array_sum(array_column($salesData, 'quantity')) }}</strong></span>
                </div>
                <div class="summary-item">
                    <span><strong>Total Revenue:</strong></span>
                    <span><strong>${{ number_format($grandTotal, 2) }}</strong></span>
                </div>
            </div>
        @else
            <p>No products were sold today.</p>
        @endif
    </div>
</body>
</html>

