<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Setting;
use Livewire\Component;

class AdminSettings extends Component
{
    public string $shop_name = '';
    public string $shop_tagline = '';
    public string $shop_address = '';
    public string $shop_phone = '';
    public string $shop_email = '';
    public string $shop_ssm = '';
    public $courier_fee = 10;
    public bool $allow_registration = true;

    // Bahan
    public ?int $editingId = null;
    public string $m_name = '';
    public string $m_code = '';
    public $m_price = '';
    public bool $showForm = false;

    public ?string $saved = null;

    public function mount()
    {
        $this->shop_name = (string) Setting::get('shop_name', 'PrintFlow');
        $this->shop_tagline = (string) Setting::get('shop_tagline', '');
        $this->courier_fee = (float) Setting::get('courier_fee', 10);
        $this->allow_registration = Setting::get('allow_registration', '1') === '1';

        // Maklumat syarikat untuk invois — diambil dari profil pemilik kedai
        // jika tetapan ini belum pernah diisi.
        $owner = auth()->user();
        $this->shop_address = (string) Setting::get('shop_address', $owner?->mailing_address ?? '');
        $this->shop_phone   = (string) Setting::get('shop_phone', $owner?->phone ?? '');
        $this->shop_email   = (string) Setting::get('shop_email', $owner?->email ?? '');
        $this->shop_ssm     = (string) Setting::get('shop_ssm', '');
    }

    public function saveSettings()
    {
        $this->validate([
            'shop_name'    => 'required|string|max:60',
            'shop_tagline' => 'nullable|string|max:80',
            'shop_address' => 'required|string|max:500',
            'shop_phone'   => 'nullable|string|max:30',
            'shop_email'   => 'nullable|email|max:120',
            'shop_ssm'     => 'nullable|string|max:60',
            'courier_fee'  => 'required|numeric|min:0|max:1000',
        ], [
            'shop_address.required' => 'Alamat kedai diperlukan kerana ia dicetak pada invois pelanggan.',
        ]);

        Setting::put('shop_name', $this->shop_name);
        Setting::put('shop_tagline', $this->shop_tagline);
        Setting::put('shop_address', $this->shop_address);
        Setting::put('shop_phone', $this->shop_phone);
        Setting::put('shop_email', $this->shop_email);
        Setting::put('shop_ssm', $this->shop_ssm);
        Setting::put('courier_fee', (string) $this->courier_fee);
        Setting::put('allow_registration', $this->allow_registration ? '1' : '0');

        $this->saved = 'Tetapan disimpan.';
    }

    /** Salin alamat surat menyurat dari profil pemilik ke tetapan kedai. */
    public function copyFromProfile()
    {
        $owner = auth()->user();

        $this->shop_address = (string) $owner?->mailing_address;
        $this->shop_phone   = (string) $owner?->phone;
        $this->shop_email   = (string) $owner?->email;

        $this->saved = 'Maklumat diambil dari profil. Klik "Simpan Tetapan" untuk kekalkan.';
    }

    public function newMaterial()
    {
        $this->reset('editingId', 'm_name', 'm_code', 'm_price');
        $this->resetValidation();
        $this->showForm = true;
    }

    public function editMaterial(int $id)
    {
        $m = Material::findOrFail($id);
        $this->editingId = $m->id;
        $this->m_name = $m->name;
        $this->m_code = $m->code;
        $this->m_price = $m->price_per_sqft;
        $this->showForm = true;
    }

    public function saveMaterial()
    {
        $this->validate([
            'm_name'  => 'required|string|max:80',
            'm_code'  => ['required', 'regex:/^[a-z0-9_]+$/', 'max:50', 'unique:materials,code,' . ($this->editingId ?? 'NULL')],
            'm_price' => 'required|numeric|min:0.01|max:9999',
        ], [
            'm_code.regex' => 'Kod: huruf kecil, nombor dan garis bawah sahaja.',
        ]);

        Material::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->m_name, 'code' => $this->m_code, 'price_per_sqft' => $this->m_price]
        );

        $this->showForm = false;
        $this->reset('editingId', 'm_name', 'm_code', 'm_price');
    }

    public function toggleMaterial(int $id)
    {
        $m = Material::findOrFail($id);
        $m->update(['is_active' => ! $m->is_active]);
    }

    public function render()
    {
        return view('livewire.admin-settings', [
            'materials' => Material::orderBy('name')->get(),
        ]);
    }
}