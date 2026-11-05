<?php

namespace App\Livewire;

use App\Models\CourierAddress;
use Livewire\Component;

class ProfileAddresses extends Component
{
    public string $mailing_address = '';
    public bool $same = false;
    public ?string $mailingSaved = null;

    // Borang alamat kurier
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $label = '';
    public string $recipient_name = '';
    public string $phone = '';
    public string $address = '';

    public function mount()
    {
        $user = auth()->user();
        $this->mailing_address = (string) $user->mailing_address;
        $this->same = (bool) $user->courier_same_as_mailing;
    }

    public function saveMailing()
    {
        $this->validate([
            'mailing_address' => ($this->same ? 'required' : 'nullable') . '|string|max:500',
        ], [
            'mailing_address.required' => 'Isi alamat surat menyurat dahulu jika alamat kurier sama.',
        ]);

        $user = auth()->user();
        $user->mailing_address = $this->mailing_address ?: null;
        $user->courier_same_as_mailing = $this->same;
        $user->save();

        $this->mailingSaved = 'Alamat disimpan.';
    }

    public function newAddress()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editAddress(int $id)
    {
        $a = auth()->user()->courierAddresses()->findOrFail($id);
        $this->editingId = $a->id;
        $this->label = $a->label;
        $this->recipient_name = $a->recipient_name;
        $this->phone = $a->phone;
        $this->address = $a->address;
        $this->showForm = true;
    }

    public function saveAddress()
    {
        $data = $this->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
        ]);

        $user = auth()->user();

        if ($this->editingId) {
            $user->courierAddresses()->findOrFail($this->editingId)->update($data);
        } else {
            $isFirst = ! $user->courierAddresses()->exists();
            $user->courierAddresses()->create($data + ['is_default' => $isFirst]);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function setDefault(int $id)
    {
        $user = auth()->user();
        $address = $user->courierAddresses()->findOrFail($id);

        $user->courierAddresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }

    public function deleteAddress(int $id)
    {
        $user = auth()->user();
        $address = $user->courierAddresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $user->courierAddresses()->oldest()->first()?->update(['is_default' => true]);
        }
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset('editingId', 'label', 'recipient_name', 'phone', 'address');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.profile-addresses', [
            'addresses' => auth()->user()->courierAddresses()->orderByDesc('is_default')->oldest()->get(),
        ]);
    }
}