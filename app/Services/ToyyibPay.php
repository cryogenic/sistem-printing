<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTopup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPay
{
    private string $url;
    private string $secret;
    private string $category;

    public function __construct()
    {
        $this->url      = rtrim((string) config('services.toyyibpay.url'), '/');
        $this->secret   = (string) config('services.toyyibpay.secret');
        $this->category = (string) config('services.toyyibpay.category');

        if ($this->url === '' || $this->secret === '' || $this->category === '') {
            throw new \RuntimeException('Konfigurasi ToyyibPay tidak lengkap. Semak .env dan config/services.php.');
        }
    }

    /** Cipta bill dan pulangkan URL pembayaran (order). */
    public function createBill(Order $order): string
    {
        $user = $order->user;

        $response = Http::asForm()
            ->timeout(20)
            ->post("{$this->url}/index.php/api/createBill", [
                'userSecretKey'           => $this->secret,
                'categoryCode'            => $this->category,
                'billName'                => substr('Order ' . $order->order_number, 0, 30),
                'billDescription'         => substr('Pembayaran ' . $order->job_title, 0, 100),
                'billPriceSetting'        => 1,
                'billPayorInfo'           => 1,
                'billAmount'              => (int) round($order->total_amount * 100), // dalam sen
                'billReturnUrl'           => route('toyyibpay.return'),
                'billCallbackUrl'         => route('toyyibpay.webhook'),
                'billExternalReferenceNo' => $order->order_number,
                'billTo'                  => $user->name,
                'billEmail'               => $user->email,
                'billPhone'               => $user->phone ?? '0123456789',
                'billSplitPayment'        => 0,
                'billPaymentChannel'      => 0, // FPX
                'billChargeToCustomer'    => 1,
            ]);

        $billCode = $response->json('0.BillCode');

        if (! $response->successful() || ! $billCode) {
            Log::error('ToyyibPay createBill gagal', [
                'order'  => $order->order_number,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Gagal mencipta bill ToyyibPay.');
        }

        $order->update(['toyyibpay_bill_code' => $billCode]);

        return "{$this->url}/{$billCode}";
    }

    /** Cipta bill untuk top-up wallet. */
    public function createTopupBill(WalletTopup $topup): string
    {
        $user = auth()->user();

        $response = Http::asForm()
            ->timeout(20)
            ->post("{$this->url}/index.php/api/createBill", [
                'userSecretKey'           => $this->secret,
                'categoryCode'            => $this->category,
                'billName'                => 'Top Up Wallet',
                'billDescription'         => 'Top up wallet RM ' . number_format($topup->amount, 2),
                'billPriceSetting'        => 1,
                'billPayorInfo'           => 1,
                'billAmount'              => (int) round($topup->amount * 100),
                'billReturnUrl'           => route('wallet.topup.return'),
                'billCallbackUrl'         => route('toyyibpay.webhook'),
                'billExternalReferenceNo' => 'TOPUP-' . $topup->id,
                'billTo'                  => $user->name,
                'billEmail'               => $user->email,
                'billPhone'               => $user->phone ?? '0123456789',
                'billSplitPayment'        => 0,
                'billPaymentChannel'      => 0,
                'billChargeToCustomer'    => 1,
            ]);

        $billCode = $response->json('0.BillCode');

        if (! $response->successful() || ! $billCode) {
            Log::error('ToyyibPay topup createBill gagal', [
                'topup'  => $topup->id,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Gagal mencipta bill top-up.');
        }

        $topup->update(['bill_code' => $billCode]);

        return "{$this->url}/{$billCode}";
    }

    /** Semak terus ke ToyyibPay sama ada bill sudah dibayar. */
    public function isPaid(string $billCode): ?array
    {
        $response = Http::asForm()
            ->timeout(20)
            ->post("{$this->url}/index.php/api/getBillTransactions", [
                'billCode'          => $billCode,
                'billpaymentStatus' => 1, // 1 = berjaya
            ]);

        $rows = $response->json();

        return (is_array($rows) && ! empty($rows[0])) ? $rows[0] : null;
    }

    /** Tandakan order dibayar (idempotent). */
    public function markPaid(Order $order, ?string $reference = null): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_method'    => 'fpx',
            'payment_status'    => 'paid',
            'payment_reference' => $reference,
        ]);
    }

    /** Sahkan bayaran ke ToyyibPay, kemudian tambah baki (hanya sekali). */
    public function creditTopup(WalletTopup $topup): bool
    {
        if ($topup->status === 'paid') {
            return true;
        }

        $tx = $topup->bill_code ? $this->isPaid($topup->bill_code) : null;
        if (! $tx) {
            return false;
        }

        DB::transaction(function () use ($topup, $tx) {
            $locked = WalletTopup::whereKey($topup->id)->lockForUpdate()->first();
            if ($locked->status === 'paid') {
                return;
            }

            $wallet = Wallet::where('user_id', $locked->user_id)->lockForUpdate()->firstOrFail();
            $wallet->increment('balance', $locked->amount);

            $locked->update([
                'status'            => 'paid',
                'payment_reference' => $tx['billpaymentInvoiceNo'] ?? null,
            ]);
        });

        return true;
    }
}