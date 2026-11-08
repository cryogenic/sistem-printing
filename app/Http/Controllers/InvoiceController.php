<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Muat turun invois PDF untuk satu order.
     * Pelanggan hanya boleh muat turun invois order miliknya sendiri.
     */
    public function downloadInvoice(Request $request, $order_id)
    {
        $query = Order::with('user');

        // Admin boleh buka invois sesiapa; pelanggan hanya order sendiri.
        if (! $this->isAdmin($request)) {
            $query->where('user_id', auth()->id());
        }

        $order = $query->findOrFail($order_id);

        // Invois hanya sah selepas dibayar (elak invois untuk order belum bayar).
        abort_unless($order->payment_status === 'paid', 403, 'Invois hanya tersedia selepas pembayaran disahkan.');

        $pdf = Pdf::loadView('invoices.template', $this->invoiceData($order))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'dejavu sans');

        $filename = 'Invois-' . $this->invoiceNumber($order) . '.pdf';

        // ?preview=1 -> papar dalam browser, selain itu muat turun.
        return $request->boolean('preview')
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    private function isAdmin(Request $request): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    private function invoiceNumber(Order $order): string
    {
        // INV-20261105-0009  (tarikh order + id order)
        return 'INV-' . $order->created_at->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Susun semua data yang template perlukan, terus dari rekod order.
     */
    private function invoiceData(Order $order): array
    {
        $material  = $order->selected_material
            ? \App\Models\Material::where('code', $order->selected_material)->first()
            : null;

        $materialName = $material?->name
            ?? ucwords(str_replace('_', ' ', (string) $order->selected_material))
            ?: '-';

        $length = (float) ($order->length ?? 0);
        $width  = (float) ($order->width ?? 0);
        $area   = ($length > 0 && $width > 0) ? round($length * $width, 2) : null;

        // Guna snapshot masa order dibuat; kalau tiada, guna harga semasa bahan.
        $rate = (float) ($order->rate_per_sqft ?? $material?->price_per_sqft ?? 0);

        $total        = (float) $order->total_amount;
        $courierFee   = (float) ($order->courier_fee ?? ($order->fulfillment_type === 'courier'
                            ? (float) Setting::get('courier_fee', 10)
                            : 0));
        $printingCost = round(max(0, $total - $courierFee), 2);

        $items = [[
            'description' => $order->job_title
                . ' — ' . $materialName
                . ($area ? " ({$length} × {$width} kaki / " . number_format($area, 2) . ' sqft)' : ''),
            'qty'         => 1,
            'unit'        => $printingCost,
            'amount'      => $printingCost,
        ]];

        if ($courierFee > 0) {
            $items[] = [
                'description' => 'Caj Penghantaran (Kurier)',
                'qty'         => 1,
                'unit'        => $courierFee,
                'amount'      => $courierFee,
            ];
        }

        $user = $order->user;

        return [
            'invoice_no'     => $this->invoiceNumber($order),
            'invoice_date'   => $order->updated_at->format('d/m/Y'),
            'order'          => $order,
            'items'          => $items,
            'subtotal'       => round(array_sum(array_column($items, 'amount')), 2),
            'total'          => $total,
            'area'           => $area,
            'rate'           => $rate,
            'material_name'  => $materialName,
            'shop_name'      => Setting::get('shop_name', config('app.name')),
            'shop_tagline'   => Setting::get('shop_tagline', ''),
            'shop_address'   => Setting::get('shop_address', $order->user?->mailing_address),
            'shop_phone'     => Setting::get('shop_phone', $order->user?->phone),
            'shop_email'     => Setting::get('shop_email', $order->user?->email),
            'shop_ssm'       => Setting::get('shop_ssm', ''),
            'customer_name'  => $user?->name ?? '-',
            'customer_email' => $user?->email ?? '-',
            'customer_phone' => $user?->phone ?? null,
            'payment_method' => match ($order->payment_method) {
                'wallet' => 'Wallet PrintFlow',
                'fpx'    => 'FPX / Online Banking',
                default  => '-',
            },
            'payment_ref'    => $order->payment_reference ?? $order->toyyibpay_bill_code ?? null,
            'paid_at'        => $order->payment_status === 'paid' ? $order->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
