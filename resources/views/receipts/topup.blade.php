<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <title>Resit {{ $receipt_no }}</title>
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
            color: #059669;
            text-align: right;
            letter-spacing: 2px;
        }
        .doc-meta { font-size: 11px; text-align: right; margin-top: 6px; line-height: 1.6; }

        .rule { border-top: 2px solid #059669; margin: 16px 0 18px; }

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

        /* Sorotan jumlah */
        .amount-box {
            border: 2px solid #059669;
            border-radius: 6px;
            padding: 16px 18px;
            margin-top: 4px;
            background-color: #f0fdf4;
        }
        .amount-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #047857;
            font-weight: bold;
        }
        .amount-value {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
            margin-top: 4px;
        }
        .amount-sub { font-size: 11px; color: #4b5563; margin-top: 6px; }

        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.items thead th {
            background-color: #059669;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: 9px 10px;
            text-align: left;
            border: 1px solid #059669;
        }
        table.items tbody td {
            padding: 9px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            line-height: 1.5;
        }
        table.items tfoot td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            font-size: 13px;
            background-color: #f9fafb;
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
            margin-top: 20px;
            padding: 12px 14px;
            background-color: #f3f4f6;
            border-left: 3px solid #059669;
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
                <div class="doc-title">RESIT</div>
                <div class="doc-meta">
                    <strong>No. Resit:</strong> {{ $receipt_no }}<br>
                    <strong>Tarikh:</strong> {{ $receipt_date }}<br>
                    <strong>Jenis:</strong> Top-Up Wallet
                </div>
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="parties">
        <tr>
            <td>
                <div class="box-label">Diterima Daripada</div>
                <div class="box-body">
                    <span class="bold">{{ $customer_name }}</span><br>
                    {{ $customer_email }}<br>
                    @if ($customer_phone)
                        {{ $customer_phone }}
                    @endif
                </div>
            </td>
            <td>
                <div class="box-label">Maklumat Pembayaran</div>
                <div class="box-body">
                    <strong>Kaedah:</strong> {{ $payment_method }}<br>
                    <strong>Status:</strong> Telah Dibayar<br>
                    <strong>Tarikh Bayar:</strong> {{ $paid_at }}<br>
                    @if ($reference)
                        <strong>Rujukan:</strong> {{ $reference }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount-label">Jumlah Top-Up</div>
        <div class="amount-value">RM {{ number_format($amount, 2) }}</div>
        <div class="amount-sub">Baki wallet selepas transaksi ini: <strong>RM {{ number_format($balance_after, 2) }}</strong></div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:60%;">Keterangan</th>
                <th style="width:20%;" class="text-center">Kuantiti</th>
                <th style="width:20%;" class="text-right">Jumlah (RM)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tambahan baki Wallet {{ $shop_name }}</td>
                <td class="text-center">1</td>
                <td class="text-right">{{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">Jumlah Dibayar</td>
                <td class="text-right">RM {{ number_format($amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width:100%;margin-top:16px;">
        <tr>
            <td style="vertical-align:top;">
                <span class="paid-stamp">Telah Dibayar</span>
            </td>
            <td class="text-right" style="font-size:10px;color:#9ca3af;">
                Dijana pada {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <div class="note">
        Resit ini adalah bukti rasmi bayaran top-up wallet anda. Baki wallet boleh digunakan untuk membayar
        tempahan cetakan dalam sistem {{ $shop_name }}. Simpan resit ini untuk rujukan.
    </div>

    <div class="footer">
        @if ($shop_address)
            <strong style="color:#6b7280;">{{ $shop_name }}</strong>
            &middot; {{ preg_replace('/\s*,\s*,\s*/', ', ', str_replace(array("\r\n", "\r", "\n"), ', ', trim($shop_address))) }}
            @if ($shop_phone) &middot; Tel: {{ $shop_phone }} @endif
            @if ($shop_email) &middot; {{ $shop_email }} @endif
            <br>
        @endif
        Resit ini dijana secara automatik oleh sistem {{ $shop_name }}.<br>
        Untuk sebarang pertanyaan, sila hubungi kami dengan menyertakan No. Resit di atas.
    </div>

</body>
</html>
