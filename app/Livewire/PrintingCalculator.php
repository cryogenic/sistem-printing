<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Material;
use App\Models\Order;
use App\Models\Setting;

class PrintingCalculator extends Component
{
    use WithFileUploads;

    public $job_title = '';
    public $remarks = '';
    public $selected_material = '';
    public $length = 1;
    public $width = 1;
    public $fulfillment_type = 'pickup';
    public $shipping_address = '';
    public $selected_address = '';
    public $artwork;
    public $total_price = 0;

    private const MIN_TOTAL = 1.50;

    public function mount()
    {
        $this->selected_material = Material::where('is_active', true)->orderBy('name')->value('code') ?? '';
        $this->calculatePrice();
    }

    public function updated($propertyName)
    {
        $this->calculatePrice();
    }

    public function updatedSelectedAddress($value)
    {
        $list = $this->savedAddresses();
        if ($value !== 'manual' && isset($list[$value])) {
            $this->shipping_address = $list[$value]['text'];
        }
    }

    public function updatedFulfillmentType($value)
    {
        if ($value === 'courier' && $this->shipping_address === '') {
            $list = $this->savedAddresses();
            if ($list) {
                $this->selected_address = array_key_first($list);
                $this->shipping_address = $list[$this->selected_address]['text'];
            }
        }
    }

    private function savedAddresses(): array
    {
        $user = auth()->user();
        $list = [];

        if ($user->courier_same_as_mailing && $user->mailing_address) {
            $list['mailing'] = ['label' => 'Alamat Surat Menyurat', 'text' => $user->mailing_address];
        } else {
            foreach ($user->courierAddresses()->orderByDesc('is_default')->oldest()->get() as $a) {
                $list['a' . $a->id] = [
                    'label' => $a->label . ($a->is_default ? ' (Utama)' : ''),
                    'text'  => $a->recipient_name . ' (' . $a->phone . ")\n" . $a->address,
                ];
            }
        }

        return $list;
    }

    private function courierFee(): float
    {
        return $this->fulfillment_type === 'courier'
            ? (float) Setting::get('courier_fee', 10)
            : 0.0;
    }

    private function activeMaterial(): ?Material
    {
        return Material::where('code', $this->selected_material)->where('is_active', true)->first();
    }

    public function calculatePrice()
    {
        $l = (float) ($this->length > 0 ? $this->length : 1);
        $w = (float) ($this->width > 0 ? $this->width : 1);
        $rate = (float) ($this->activeMaterial()?->price_per_sqft ?? 0);

        $this->total_price = round(max(self::MIN_TOTAL, ($l * $w * $rate) + $this->courierFee()), 2);
    }

    public function submitOrder()
    {
        $rules = [
            'job_title'         => 'required|min:3',
            'selected_material' => 'required',
            'length'            => 'required|numeric|min:0.1',
            'width'             => 'required|numeric|min:0.1',
            'fulfillment_type'  => 'required|in:pickup,courier',
            'artwork'           => 'nullable|file|max:10240',
        ];

        if ($this->fulfillment_type === 'courier') {
            $rules['shipping_address'] = 'required|min:5';
        }

        $this->validate($rules, [
            'job_title.required'        => 'Sila masukkan tajuk tempahan.',
            'job_title.min'             => 'Tajuk tempahan sekurang-kurangnya 3 huruf.',
            'shipping_address.required' => 'Alamat penghantaran diperlukan untuk pilihan Kurier.',
        ]);

        $material = $this->activeMaterial();
        if (! $material) {
            $this->addError('selected_material', 'Bahan yang dipilih tidak tersedia. Sila pilih bahan lain.');
            return;
        }

        // Kira semula di server
        $this->calculatePrice();
        $courier = $this->courierFee();

        $artworkPath = $this->artwork ? $this->artwork->store('artworks', 'public') : null;

        $order_number = 'ORD-' . date('Ymd') . '-' . random_int(1000, 9999);

        Order::create([
            'order_number'      => $order_number,
            'job_title'         => $this->job_title,
            'selected_material' => $this->selected_material,
            'length'            => $this->length,
            'width'             => $this->width,
            'remarks'           => $this->remarks,
            'fulfillment_type'  => $this->fulfillment_type,
            'shipping_address'  => $this->fulfillment_type === 'courier' ? $this->shipping_address : null,
            'user_id'           => auth()->id(),
            'total_amount'      => $this->total_price,
            'rate_per_sqft'     => $material->price_per_sqft,
            'courier_fee'       => $courier,
            'payment_status'    => 'unpaid',
            'production_status' => $artworkPath ? 'processing' : 'pending_artwork',
            'artwork_path'      => $artworkPath,
        ]);

        session()->flash('success', 'Tempahan berjaya direkodkan! Sila semak rumusan pesanan anda.');
        return redirect()->route('order.summary', ['orderNumber' => $order_number]);
    }

    public function render()
    {
        return view('livewire.printing-calculator', [
            'materials'      => Material::where('is_active', true)->orderBy('name')->get(),
            'courierFee'     => (float) Setting::get('courier_fee', 10),
            'savedAddresses' => $this->savedAddresses(),
        ]);
    }
}