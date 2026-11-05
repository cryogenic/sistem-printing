<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\WalletTopup;
use Livewire\Attributes\Url;
use Livewire\Component;

class BillingHistory extends Component
{
    #[Url(as: 'tab', except: 'topup')]
    public string $tab = 'topup';

    public function setTab(string $tab)
    {
        $this->tab = in_array($tab, ['topup', 'payments'], true) ? $tab : 'topup';
    }

    public function render()
    {
        $uid = auth()->id();

        return view('livewire.billing-history', [
            'topups'   => WalletTopup::where('user_id', $uid)->latest()->limit(100)->get(),
            'payments' => Order::where('user_id', $uid)->where('payment_status', 'paid')->latest('updated_at')->limit(100)->get(),
        ]);
    }
}