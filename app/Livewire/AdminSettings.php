<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Setting;
use Livewire\Component;

class AdminSettings extends Component
{
    public string $shop_name = '';
    public string $shop_tagline = '';
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
    }

    public function saveSettings()
    {
        $this->validate([
            'shop_name'    => 'required|string|max:60',
            'shop_tagline' => 'nullable|string|max:80',
            'courier_fee'  => 'required|numeric|min:0|max:1000',
        ]);

        Setting::put('shop_name', $this->shop_name);
        Setting::put('shop_tagline', $this->shop_tagline);
        Setting::put('courier_fee', (string) $this->courier_fee);
        Setting::put('allow_registration', $this->allow_registration ? '1' : '0');

        $this->saved = 'Tetapan disimpan.';
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