<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invois Rasmi</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header td { padding: 5px; vertical-align: top; }
        .title { font-size: 24px; font-weight: bold; color: #4F46E5; }
        .details-table, .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; }
        .total-row td { font-weight: bold; }
        .text-right { text-align: right !important; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td class="title">SISTEM CETAK JWORKS</td>
                <td class="text-right">
                    <strong>Invois #:</strong> {{ $invoice_no }}<br>
                    <strong>Tarikh:</strong> {{ $date }}
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td>
                    <strong>Kepada:</strong><br>
                    {{ $customer_name }}<br>
                    {{ $company_name }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Perkara / Butiran Cetakan</th>
                    <th>Kuantiti</th>
                    <th class="text-right">Harga (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td class="text-right">{{ number_format($item['price'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="text-right">Jumlah Keseluruhan:</td>
                    <td class="text-right">RM {{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>