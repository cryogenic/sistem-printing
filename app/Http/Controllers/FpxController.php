<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WalletTopup;
use App\Services\ToyyibPay;
use Illuminate\Http\Request;

class FpxController extends Controller
{
    // Pelanggan dipulangkan ke sini selepas bayar order (GET, dalam sesi log masuk)
    public function return(Request $request, ToyyibPay $toyyib)
    {
        $order = Order::where('order_number', $request->query('order_id'))
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $tx = $order->toyyibpay_bill_code
            ? $toyyib->isPaid($order->toyyibpay_bill_code)
            : null;

        if ($tx) {
            $toyyib->markPaid($order, $tx['billpaymentInvoiceNo'] ?? $request->query('transaction_id'));
            session()->flash('success', 'Pembayaran FPX berjaya! Terima kasih.');
        } else {
            session()->flash('error', 'Pembayaran belum disahkan atau tidak berjaya. Sila cuba lagi.');
        }

        return redirect()->route('order.summary', $order->order_number);
    }

    // Pelanggan dipulangkan ke sini selepas top-up wallet
    public function topupReturn(Request $request, ToyyibPay $toyyib)
    {
        $topup = WalletTopup::where('bill_code', $request->query('billcode'))
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($toyyib->creditTopup($topup)) {
            session()->flash('success', 'Top-up RM ' . number_format($topup->amount, 2) . ' berjaya! Baki wallet dikemas kini.');
        } else {
            session()->flash('error', 'Pembayaran top-up belum disahkan atau tidak berjaya.');
        }

        return redirect()->route('wallet.topup');
    }

    // ToyyibPay hantar POST dari server mereka (tiada sesi, tiada CSRF)
    public function webhook(Request $request, ToyyibPay $toyyib)
    {
        $ref = (string) $request->input('order_id');

        // Top-up wallet
        if (str_starts_with($ref, 'TOPUP-')) {
            $topup = WalletTopup::find((int) substr($ref, 6));
            if ($topup && $topup->bill_code === $request->input('billcode')) {
                $toyyib->creditTopup($topup);
            }
            return response('OK');
        }

        // Bayaran order
        $order = Order::where('order_number', $ref)->first();

        if ($order && $order->toyyibpay_bill_code === $request->input('billcode')) {
            if ($tx = $toyyib->isPaid($order->toyyibpay_bill_code)) {
                $toyyib->markPaid($order, $tx['billpaymentInvoiceNo'] ?? $request->input('refno'));
            }
        }

        return response('OK');
    }
}