<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .alert-box {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-item {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
        }
        .value {
            color: #111827;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px;">
        <p>Hello,</p>
        <p>This is to notify you that a product is running low on stock and may need to be restocked soon.</p>
    </div>

    <div class="header">
        <h1 style="margin: 0;">Low Stock Alert</h1>
    </div>

    <div class="content">

        <div class="alert-box">
            <h2 style="margin-top: 0; color: #dc2626;">{{ $product->name }}</h2>
            
            <div class="info-item">
                <span class="label">Current Stock:</span>
                <span class="value">{{ $product->stock_quantity }} units</span>
            </div>
            
            <div class="info-item">
                <span class="label">Low Stock Threshold:</span>
                <span class="value">{{ $threshold }} units</span>
            </div>
        </div>

        <p>Please consider restocking this product to avoid running out of stock.</p>

        <p>Best regards,<br>System Notification</p>
    </div>
</body>
</html>

