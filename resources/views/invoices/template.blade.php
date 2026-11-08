<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <title>Invois {{ $invoice_no }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
        }
        .muted { color: #6b7280; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        /* Header */
        table.header { width: 100%; border-collapse: collapse; }
        table.header td { vertical-align: top; padding: 0; }
        .shop-name { font-size: 22px; font-weight: bold; color: #4f46e5; letter-spacing: .3px; }
        .shop-tagline { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .shop-address {
            font-size: 10px;
            color: #4b5563;
            margin-top: 8px;
            line-height: 1.55;
            max-width: 280px;
        }
        .shop-address strong { color: #374151; }
        .doc-title {
            font-size: 26px;
            font-weight: bold;
            color: #111827;
            text-align: right;
            letter-spacing: 2px;
        }
        .doc-meta { font-size: 11px; text-align: right; margin-top: 6px; line-height: 1.6; }

        .rule { border-top: 2px solid #4f46e5; margin: 16px 0 18px; }

        /* Party boxes */
        table.parties { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.parties td { width: 50%; vertical-align: top; padding: 0; }
        table.parties td:last-child { padding-left: 24px; }
        .box-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .box-body { line-height: 1.6; }

        /* Items */
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items thead th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: 9px 10px;
            text-align: left;
            border: 1px solid #4f46e5;
        }
        table.items tbody td {
            padding: 9px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            line-height: 1.5;
        }
        table.items tbody tr:nth-child(even) td { background-color: #f9fafb; }

        /* Totals */
        table.totals { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.totals td { padding: 7px 10px; }
        table.totals .label { text-align: right; color: #4b5563; width: 70%; }
        table.totals .value { text-align: right; width: 30%; }
        table.totals tr.grand td {
            border-top: 2px solid #111827;
            font-size: 15px;
            font-weight: bold;
            color: #111827;
            padding-top: 10px;
        }
        .paid-stamp {
            display: inline-block;
            border: 2px solid #059669;
            color: #059669;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 2px;
            padding: 6px 14px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .note {
            margin-top: 22px;
            padding: 12px 14px;
            background-color: #f3f4f6;
            border-left: 3px solid #4f46e5;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }
        .footer {
            margin-top: 26px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="shop-name">{{ $shop_name }}</div>
                @if ($shop_tagline)
                    <div class="shop-tagline">{{ $shop_tagline }}</div>
                @endif
                <div class="shop-address">
                    @if ($shop_address)
                        {!! nl2br(e($shop_address)) !!}<br>
                    @endif
                    @if ($shop_phone)
                        Tel: {{ $shop_phone }}<br>
                    @endif
                    @if ($shop_email)
                        {{ $shop_email }}<br>
                    @endif
                    @if ($shop_ssm)
                        <strong>No. SSM:</strong> {{ $shop_ssm }}
                    @endif
                </div>
            </td>
            <td>
                <div class="doc-title">INVOIS</div>
                <div class="doc-meta">
                    <strong>No. Invois:</strong> {{ $invoice_no }}<br>
                    <strong>Tarikh:</strong> {{ $invoice_date }}<br>
                    <strong>No. Pesanan:</strong> {{ $order->order_number }}
                </div>
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="parties">
        <tr>
            <td>
                <div class="box-label">Dibilkan Kepada</div>
                <div class="box-body">
                    <span class="bold">{{ $customer_name }}</span><br>
                    {{ $customer_email }}<br>
                    @if ($customer_phone)
                        {{ $customer_phone }}<br>
                    @endif
                    @if ($order->fulfillment_type === 'courier' && $order->shipping_address)
                        <span class="muted">{{ nl2br(e($order->shipping_address)) }}</span>
                    @endif
                </div>
            </td>
            <td>
                <div class="box-label">Maklumat Pembayaran</div>
                <div class="box-body">
                    <strong>Kaedah:</strong> {{ $payment_method }}<br>
                    <strong>Status:</strong> {{ $order->payment_status === 'paid' ? 'Telah Dibayar' : 'Belum Dibayar' }}<br>
                    @if ($paid_at)
                        <strong>Tarikh Bayar:</strong> {{ $paid_at }}<br>
                    @endif
                    @if ($payment_ref)
                        <strong>Rujukan:</strong> {{ $payment_ref }}<br>
                    @endif
                    <strong>Penerimaan:</strong>
                    {{ $order->fulfillment_type === 'courier' ? 'Kurier' : 'Ambil Sendiri' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:52%;">Perkara / Butiran Cetakan</th>
                <th style="width:10%;" class="text-center">Kuantiti</th>
                <th style="width:19%;" class="text-right">Harga Seunit (RM)</th>
                <th style="width:19%;" class="text-right">Jumlah (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ $item['qty'] }}</td>
                    <td class="text-right">{{ number_format($item['unit'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subjumlah</td>
            <td class="value">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Cukai (SST)</td>
            <td class="value">0.00</td>
        </tr>
        <tr class="grand">
            <td class="label">Jumlah Keseluruhan</td>
            <td class="value">RM {{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <table style="width:100%;margin-top:16px;">
        <tr>
            <td style="vertical-align:top;">
                @if ($order->payment_status === 'paid')
                    <span class="paid-stamp">Telah Dibayar</span>
                @endif
            </td>
            <td class="text-right" style="font-size:10px;color:#9ca3af;">
                Dijana pada {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    @if ($order->remarks)
        <div class="note">
            <strong>Catatan pesanan:</strong> {{ $order->remarks }}
        </div>
    @endif

    <div class="footer">
        @if ($shop_address)
            <strong style="color:#6b7280;">{{ $shop_name }}</strong>
            &middot; {{ preg_replace('/\s*,\s*,\s*/', ', ', str_replace(array("\r\n", "\r", "\n"), ', ', trim($shop_address))) }}
            @if ($shop_phone) &middot; Tel: {{ $shop_phone }} @endif
            @if ($shop_email) &middot; {{ $shop_email }} @endif
            <br>
        @endif
        Invois ini dijana secara automatik oleh sistem {{ $shop_name }}.<br>
        Terima kasih atas tempahan anda. Untuk sebarang pertanyaan, sila hubungi kami dengan menyertakan No. Invois di atas.
    </div>

</body>
</html>
