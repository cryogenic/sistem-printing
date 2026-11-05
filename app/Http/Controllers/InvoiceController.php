<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function downloadInvoice($order_id)
    {
        // Dalam sistem sebenar, anda akan panggil data dari pangkalan data:
        // $order = Order::with('user', 'items')->findOrFail($order_id);
        
        // Data simulasi untuk ujian paparan
        $data = [
            'invoice_no' => 'INV-20261022-' . str_pad($order_id, 4, '0', STR_PAD_LEFT),
            'date' => date('d/m/Y'),
            'customer_name' => auth()->user()->name,
            'company_name' => auth()->user()->company_name ?? 'Pelanggan Individu',
            'items' => [
                ['description' => 'Cetak Banner Tarpaulin 380gsm (10x4 ft)', 'qty' => 1, 'price' => 60.00],
                ['description' => 'Kemasan PVC Pipe Atas Bawah', 'qty' => 1, 'price' => 5.00],
            ],
            'total' => 65.00
        ];

        // Rujuk fail templat blade 'resources/views/invoices/template.blade.php'
        $pdf = Pdf::loadView('invoices.template', $data);

        // Muat turun fail secara automatik
        return $pdf->download('Invois-' . $data['invoice_no'] . '.pdf');
        
        // Nota: Jika mahu ia terpapar di browser tanpa download terus, gunakan:
        // return $pdf->stream('Invois-' . $data['invoice_no'] . '.pdf');
    }
}