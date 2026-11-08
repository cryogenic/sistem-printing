<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\WalletTopup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TopupReceiptController extends Controller
{
    /**
     * Muat turun resit PDF untuk satu transaksi top-up wallet.
     * Pelanggan hanya boleh muat turun resit top-up miliknya sendiri.
     */
    public function download(Request $request, $topup_id)
    {
        $query = WalletTopup::with('user');

        // Admin boleh buka resit sesiapa; pelanggan hanya miliknya sendiri.
        if (! $this->isAdmin($request)) {
            $query->where('user_id', auth()->id());
        }

        $topup = $query->findOrFail($topup_id);

        // Resit hanya sah selepas bayaran disahkan.
        abort_unless($topup->status === 'paid', 403, 'Resit hanya tersedia selepas pembayaran disahkan.');

        $pdf = Pdf::loadView('receipts.topup', $this->receiptData($topup))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'dejavu sans');

        $filename = 'Resit-' . $this->receiptNumber($topup) . '.pdf';

        // ?preview=1 -> papar dalam browser, selain itu muat turun.
        return $request->boolean('preview')
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    private function isAdmin(Request $request): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /** RCP-20261108-0003 */
    private function receiptNumber(WalletTopup $topup): string
    {
        return 'RCP-' . $topup->created_at->format('Ymd') . '-' . str_pad($topup->id, 4, '0', STR_PAD_LEFT);
    }

    private function receiptData(WalletTopup $topup): array
    {
        $user = $topup->user;
        $amount = (float) $topup->amount;

        // Baki selepas top-up ini: jumlah semua top-up berbayar sehingga transaksi ini.
        $balanceAfter = (float) WalletTopup::where('user_id', $topup->user_id)
            ->where('status', 'paid')
            ->where('id', '<=', $topup->id)
            ->sum('amount');

        return [
            'receipt_no'     => $this->receiptNumber($topup),
            'receipt_date'   => $topup->updated_at->format('d/m/Y'),
            'paid_at'        => $topup->updated_at->format('d/m/Y H:i'),
            'topup'          => $topup,
            'amount'         => $amount,
            'balance_after'  => $balanceAfter,
            'reference'      => $topup->payment_reference ?? $topup->bill_code ?? null,
            'payment_method' => 'FPX / Online Banking (ToyyibPay)',
            'customer_name'  => $user?->name ?? '-',
            'customer_email' => $user?->email ?? '-',
            'customer_phone' => $user?->phone ?? null,
            'shop_name'      => Setting::get('shop_name', config('app.name')),
            'shop_tagline'   => Setting::get('shop_tagline', ''),
            'shop_address'   => Setting::get('shop_address', $user?->mailing_address),
            'shop_phone'     => Setting::get('shop_phone', $user?->phone),
            'shop_email'     => Setting::get('shop_email', $user?->email),
            'shop_ssm'       => Setting::get('shop_ssm', ''),
        ];
    }
}
