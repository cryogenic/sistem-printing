<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Models\WalletTopup as Topup;
use App\Services\ToyyibPay;
use Livewire\Component;

class WalletTopup extends Component
{
    public $amount = 50;
    public ?string $errorMessage = null;

    public function setAmount($value)
    {
        $this->amount = (int) $value;
    }

    public function topup()
    {
        $this->errorMessage = null;

        $this->validate([
            'amount' => 'required|numeric|min:5|max:1000',
        ], [
            'amount.min' => 'Jumlah minimum top-up ialah RM 5.',
            'amount.max' => 'Jumlah maksimum top-up ialah RM 1,000.',
            'amount.numeric' => 'Sila masukkan jumlah yang sah.',
        ]);

        try {
            $topup = Topup::create([
                'user_id' => auth()->id(),
                'amount'  => round((float) $this->amount, 2),
            ]);
            $url = app(ToyyibPay::class)->createTopupBill($topup);
        } catch (\Throwable $e) {
            report($e);
            $this->errorMessage = app()->isLocal()
                ? 'Top-up gagal: ' . $e->getMessage()
                : 'Tidak dapat menyambung ke ToyyibPay. Sila cuba lagi.';
            return;
        }

        return $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.wallet-topup', [
            'balance' => Wallet::where('user_id', auth()->id())->value('balance') ?? 0,
            'history' => Topup::where('user_id', auth()->id())->where('status', 'paid')->latest()->take(10)->get(),
        ]);
    }
}