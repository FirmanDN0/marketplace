<!DOCTYPE html>
<html>
<head>
    <title>Provider Earnings Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #28a745; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .section-title { font-size: 14px; font-bold; margin-bottom: 10px; color: #28a745; border-left: 4px solid #28a745; padding-left: 8px; }
        
        .stats-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .stats-table td { width: 25%; padding: 15px; border: 1px solid #eee; }
        .stat-label { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 14px; font-weight: bold; color: #333; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        table.data-table td { border: 1px solid #dee2e6; padding: 8px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Provider Report</h1>
        <p>Earnings & Orders Summary for {{ $provider->name }}</p>
        <p style="font-size: 10px;">Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section-title">Account Summary</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Total Services</div>
                <div class="stat-value">{{ $stats['total_services'] }}</div>
            </td>
            <td>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
            </td>
            <td>
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $stats['completed_orders'] }}</div>
            </td>
            <td>
                <div class="stat-label">Total Earned</div>
                <div class="stat-value">Rp {{ number_format($stats['total_earned'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Order History (Last 100)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Earnings</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->service->title }}</td>
                <td>Rp {{ number_format($order->provider_amount, 0, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $order->status === 'completed' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : '') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} ServeMix Marketplace Platform - Provider Report
    </div>
</body>
</html>
