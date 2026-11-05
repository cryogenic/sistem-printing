<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class ToyyibPayController extends Controller
{
    // Fungsi 1: Hantar pengguna ke halaman FPX
    public function topupWallet(Request $request)
    {
        $user = auth()->user();
        $amount = 100; // Contoh: Nilai topup tetap RM100. (Boleh diubah supaya dinamis)

        $data = [
            'userSecretKey' => env('nj3sda89-woo0-5l53-0byf-41z7wgy9iegf'),
            'categoryCode' => env('o7dwc2n6'),
            'billName' => 'Top-up Wallet Ejen',
            'billDescription' => 'Tambah nilai dompet ' . $user->name,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $amount * 100, // ToyyibPay baca nilai dalam Sen (RM100 = 10000 sen)
            'billReturnUrl' => route('toyyibpay.callback'),
            'billCallbackUrl' => route('toyyibpay.webhook'),
            'billExternalReferenceNo' => 'TOPUP-' . time() . '-' . $user->id,
            'billTo' => $user->name,
            'billEmail' => $user->email,
            'billPhone' => '0123456789',
        ];

        $url = env('TOYYIBPAY_URL') . 'index.php/api/createBill';
        $response = Http::asForm()->post($url, $data);
        $billCode = $response[0]['BillCode'];

        // Redirect pengguna ke halaman pembayaran ToyyibPay
        return redirect(env('TOYYIBPAY_URL') . $billCode);
    }

    // Fungsi 2: Proses maklum balas selepas pelanggan bayar
    public function paymentCallback(Request $request)
    {
        $status_id = $request->status_id;
        $order_id = $request->order_id; // Mengandungi 'TOPUP-masa-UserID'
        
        // Pecahkan order_id untuk dapatkan User ID
        $parts = explode('-', $order_id);
        $user_id = end($parts); 
        $amount_paid = 100; // Dalam sistem sebenar, rujuk pangkalan data untuk amaun sebenar

        if ($status_id == 1) { // 1 = Pembayaran Berjaya
            DB::transaction(function () use ($user_id, $amount_paid) {
                // Cari atau cipta dompet pengguna
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $user_id],
                    ['balance' => 0.00]
                );

                // Tambah baki
                $wallet->balance += $amount_paid;
                $wallet->save();

                // Rekod audit transaksi masuk
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $amount_paid,
                    'type' => 'in',
                    'description' => 'Top-up FPX ToyyibPay',
                ]);
            });

            return redirect()->route('dashboard')->with('success', 'Top-up berjaya! Baki dompet telah dikemas kini.');
        } 

        return redirect()->route('dashboard')->with('error', 'Pembayaran gagal atau dibatalkan.');
    }
}