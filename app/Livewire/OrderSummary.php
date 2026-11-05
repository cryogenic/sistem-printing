<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Wallet;
use App\Services\ToyyibPay;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderSummary extends Component
{
    public Order $order;
    public ?string $errorMessage = null;

    public function mount($orderNumber)
    {
        $this->order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function payWithWallet()
    {
        $this->errorMessage = null;

        try {
            DB::transaction(function () {
                $order = Order::where('id', $this->order->id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($order->payment_status === 'paid') {
                    return;
                }

                $wallet = Wallet::where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($wallet->balance < $order->total_amount) {
                    throw new \RuntimeException('Baki wallet tidak mencukupi. Sila top-up atau bayar guna FPX.');
                }

                $wallet->decrement('balance', $order->total_amount);

                $order->update([
                    'payment_method' => 'wallet',
                    'payment_status' => 'paid',
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }

        $this->order->refresh();
        session()->flash('success', 'Pembayaran melalui wallet berjaya!');
    }

    public function payWithFpx()
    {
        $this->errorMessage = null;

        if ($this->order->payment_status === 'paid') {
            return;
        }

        try {
            // Dipanggil di dalam try supaya ralat konfigurasi pun ditangkap
            $url = app(ToyyibPay::class)->createBill($this->order);
        } catch (\Throwable $e) {
            report($e);
            $this->errorMessage = app()->isLocal()
                ? 'FPX gagal: ' . $e->getMessage()
                : 'Tidak dapat menyambung ke ToyyibPay. Sila cuba sebentar lagi.';
            return;
        }

        return $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.order-summary', [
            'materials' => [
                'tarpaulin_380'   => ['label' => 'Tarpaulin 380gsm', 'rate' => 1.50],
                'synthetic_paper' => ['label' => 'Synthetic Paper',  'rate' => 2.50],
                'sticker_white'   => ['label' => 'Sticker White PP', 'rate' => 3.00],
            ],
            'walletBalance' => Wallet::where('user_id', auth()->id())->value('balance') ?? 0,
        ]);
    }
}