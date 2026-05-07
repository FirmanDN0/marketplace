<!DOCTYPE html>
<html>
<head>
    <title>Platform Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4A90E2; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #4A90E2; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .section-title { font-size: 14px; font-bold; margin-bottom: 10px; color: #4A90E2; border-left: 4px solid #4A90E2; padding-left: 8px; }
        
        .stats-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .stats-table td { width: 33.33%; padding: 15px; border: 1px solid #eee; }
        .stat-label { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #333; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        table.data-table td { border: 1px solid #dee2e6; padding: 8px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SERVE MIX</h1>
        <p>Marketplace Platform - Transactional Report</p>
        <p style="font-size: 10px;">Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section-title">Revenue Summary</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value">Rp {{ number_format($revenue['today'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="stat-label">Monthly Revenue</div>
                <div class="stat-value">Rp {{ number_format($revenue['this_month'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">Rp {{ number_format($revenue['total'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Order Statistics</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $orders['total'] }}</div>
            </td>
            <td>
                <div class="stat-label">Completed</div>
                <div class="stat-value" style="color: #28a745;">{{ $orders['completed'] }}</div>
            </td>
            <td>
                <div class="stat-label">Cancelled</div>
                <div class="stat-value" style="color: #dc3545;">{{ $orders['cancelled'] }}</div>
            </td>
        </tr>
    </table>

    <div style="page-break-after: always;"></div>

    <div class="section-title">Recent Transactions (Last 20)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
            <tr>
                <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->created_at->format('d/m/y') }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->service->title }}</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $order->status === 'completed' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} ServeMix Marketplace Platform - UKL Project
    </div>
</body>
</html>
