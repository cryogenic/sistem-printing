<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfilePhoto extends Component
{
    use WithFileUploads;

    public $photo;

    public function save()
    {
        $this->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'photo.required' => 'Sila pilih gambar.',
            'photo.image'    => 'Fail mesti gambar.',
            'photo.mimes'    => 'Format dibenarkan: JPG, PNG, WEBP.',
            'photo.max'      => 'Saiz maksimum 2MB.',
        ]);

        $user = auth()->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $this->photo->store('avatars', 'public');
        $user->save();

        session()->flash('status', 'Gambar profil dikemas kini.');
        return $this->redirect(route('profile.edit'));
    }

    public function remove()
    {
        $user = auth()->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
            $user->save();
        }

        session()->flash('status', 'Gambar profil dibuang.');
        return $this->redirect(route('profile.edit'));
    }

    public function render()
    {
        return view('livewire.profile-photo');
    }
}