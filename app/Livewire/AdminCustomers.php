<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminCustomers extends Component
{
    public string $search = '';

    // Daftar pelanggan
    public bool $showForm = false;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public ?string $tempPassword = null;
    public ?string $createdEmail = null;

    // Laras baki
    public ?int $adjustingId = null;
    public string $direction = 'credit';
    public $amount = '';
    public string $reason = '';
    public ?string $message = null;
    public ?string $error = null;

    public function createCustomer()
    {
        $this->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'E-mel ini sudah didaftarkan.',
        ]);

        $password = Str::random(10);

        // role tidak dalam $fillable, nilai lalai pangkalan data ialah customer
        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone ?: null,
            'password' => $password,
        ]);

        $this->tempPassword = $password;
        $this->createdEmail = $this->email;
        $this->reset('name', 'email', 'phone', 'showForm');
    }

    public function dismissPassword()
    {
        $this->tempPassword = null;
        $this->createdEmail = null;
    }

    public function openAdjust(int $id)
    {
        $this->adjustingId = $id;
        $this->reset('amount', 'reason', 'message', 'error');
        $this->direction = 'credit';
        $this->resetValidation();
    }

    public function saveAdjust()
    {
        $this->error = null;

        $this->validate([
            'direction' => 'required|in:credit,debit',
            'amount'    => 'required|numeric|min:0.01|max:10000',
            'reason'    => 'required|string|max:200',
        ], [
            'reason.required' => 'Sila nyatakan sebab laras baki.',
        ]);

        $customer = User::where('role', '!=', 'admin')->findOrFail($this->adjustingId);

        try {
            DB::transaction(function () use ($customer) {
                $wallet = Wallet::where('user_id', $customer->id)->lockForUpdate()->firstOrFail();
                $amount = round((float) $this->amount, 2);

                if ($this->direction === 'debit') {
                    if ($wallet->balance < $amount) {
                        throw new \RuntimeException('Baki pelanggan tidak mencukupi untuk ditolak.');
                    }
                    $wallet->decrement('balance', $amount);
                } else {
                    $wallet->increment('balance', $amount);
                }

                WalletTransaction::create([
                    'wallet_id'   => $wallet->id,
                    'amount'      => $amount,
                    'type'        => $this->direction,
                    'description' => 'Laras oleh admin (' . auth()->user()->name . '): ' . $this->reason,
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
            return;
        }

        $this->message = 'Baki ' . $customer->name . ' dikemas kini.';
        $this->adjustingId = null;
        $this->reset('amount', 'reason');
    }

    public function render()
    {
        $customers = User::where('role', '!=', 'admin')
            ->when($this->search !== '', function ($q) {
                $s = '%' . $this->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s));
            })
            ->with('wallet')
            ->withCount('orders')
            ->orderBy('name')
            ->get();

        return view('livewire.admin-customers', ['customers' => $customers]);
    }
}