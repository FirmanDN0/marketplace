<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }
        .company-details {
            text-align: right;
            color: #666;
            font-size: 12px;
        }
        .invoice-info {
            width: 100%;
            margin-bottom: 40px;
        }
        .invoice-info td {
            vertical-align: top;
            width: 50%;
        }
        .info-title {
            font-weight: bold;
            color: #444;
            margin-bottom: 5px;
        }
        .table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .table th {
            background: #f8fafc;
            padding: 12px;
            border-bottom: 2px solid #e2e8f0;
            color: #334155;
            font-weight: bold;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .amount {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: none;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .badge {
            background: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td>
                    <div class="title">ServeMix</div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">Marketplace Layanan Profesional</div>
                </td>
                <td class="company-details">
                    <strong>PT. ServeMix Digital Nusantara</strong><br>
                    Jl. Teknologi No. 88, Jakarta Selatan<br>
                    support@servemix.com<br>
                    +62 811 2345 6789
                </td>
            </tr>
        </table>

        <table class="invoice-info">
            <tr>
                <td>
                    <div class="info-title">Ditagihkan Kepada:</div>
                    <strong>{{ $order->customer->name }}</strong><br>
                    {{ $order->customer->email }}<br>
                    @if($order->customer->profile && $order->customer->profile->city)
                        {{ $order->customer->profile->city }}, {{ $order->customer->profile->country ?? 'Indonesia' }}
                    @else
                        Klien ServeMix
                    @endif
                </td>
                <td style="text-align: right;">
                    <div class="info-title">Detail Invoice:</div>
                    <strong>Nomor:</strong> {{ $order->order_number }}<br>
                    <strong>Tanggal:</strong> {{ optional($order->payment)->paid_at ? $order->payment->paid_at->format('d F Y') : $order->created_at->format('d F Y') }}<br>
                    <strong>Status:</strong> <span class="badge">LUNAS</span>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th>Penyedia Jasa</th>
                    <th class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $order->service->title }}</strong><br>
                        <span style="color: #666; font-size: 12px;">Paket: {{ ucfirst($order->package->type ?? 'Custom') }}</span>
                    </td>
                    <td>
                        {{ $order->provider->name }}<br>
                        <span style="color: #666; font-size: 12px;">{{ $order->provider->email }}</span>
                    </td>
                    <td class="amount">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                </tr>
                
                {{-- Platform fee is usually inclusive in the total price that the customer sees --}}
                
                <tr>
                    <td colspan="2" style="text-align: right; border-bottom: none; padding-top: 20px;">Subtotal Jasa:</td>
                    <td class="amount" style="border-bottom: none; padding-top: 20px;">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; border-bottom: none;">Biaya Layanan / PPN (10%):</td>
                    <td class="amount" style="border-bottom: none;">Rp {{ number_format($order->tax_fee, 0, ',', '.') }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td colspan="2" style="text-align: right; border-bottom: none; color: #16a34a;">Diskon Promo ({{ optional($order->voucher)->code }}):</td>
                    <td class="amount" style="border-bottom: none; color: #16a34a;">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; font-size: 18px;">Total Pembayaran:</td>
                    <td class="amount" style="font-size: 18px; color: #2563eb;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 40px;">
            <div class="info-title">Informasi Pembayaran:</div>
            <p style="margin-top: 5px; font-size: 13px; color: #555;">
                Metode Pembayaran: <strong>{{ strtoupper(optional($order->payment)->payment_method ?? 'WALLET') }}</strong><br>
                ID Transaksi: {{ optional($order->payment)->gateway_transaction_id ?? '-' }}<br>
                Catatan: Pembayaran telah diterima secara penuh oleh sistem rekening bersama ServeMix.
            </p>
        </div>

        <div class="footer">
            Ini adalah tanda terima elektronik yang sah dan dihasilkan oleh sistem komputer. Dokumen ini tidak memerlukan tanda tangan fisik.<br>
            Terima kasih telah menggunakan layanan ServeMix.
        </div>
    </div>
</body>
</html>
