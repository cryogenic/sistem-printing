<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use Livewire\Attributes\Url;

class OrderList extends Component
{
    use WithFileUploads;

    public $artwork;
    public $uploading_order_id = null;

    #[Url(as: 'payment', except: '')]
    public string $paymentFilter = '';

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    #[Url(as: 'sort', except: 'newest')]
    public string $sortDate = 'newest';

    #[Url(as: 'from', except: '')]
    public string $dateFrom = '';

    #[Url(as: 'to', except: '')]
    public string $dateTo = '';

    public function resetFilters()
    {
        $this->reset('paymentFilter', 'statusFilter', 'sortDate', 'dateFrom', 'dateTo');
    }

    public function setUploadOrder($id)
    {
        $this->uploading_order_id = $id;
        $this->artwork = null;
    }

    public function saveArtwork()
    {
        $this->validate([
            'artwork' => 'required|file|mimes:pdf,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        $order = Order::find($this->uploading_order_id);

        if ($order && $order->user_id == auth()->id()) {
            $path = $this->artwork->store('artworks', 'public');

            $order->artwork_path = $path;
            $order->production_status = 'processing';
            $order->save();

            session()->flash('success', 'Artwork berjaya dihantar! Status ditukar ke Processing.');
            $this->uploading_order_id = null;
            $this->artwork = null;
        }
    }

    private function validDate(string $value): ?string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false
            ? $value
            : null;
    }

    public function render()
    {
        $query = Order::where('user_id', auth()->id());

        if (in_array($this->paymentFilter, ['paid', 'unpaid'], true)) {
            $query->where('payment_status', $this->paymentFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('production_status', $this->statusFilter);
        }

        $from = $this->validDate($this->dateFrom);
        $to   = $this->validDate($this->dateTo);

        // Kalau terbalik, tukar ganti
        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query
            ->orderBy('created_at', $this->sortDate === 'oldest' ? 'asc' : 'desc')
            ->get();

        return view('livewire.order-list', [
            'orders'   => $orders,
            'statuses' => Order::where('user_id', auth()->id())
                                ->distinct()
                                ->pluck('production_status'),
        ]);
    }
}