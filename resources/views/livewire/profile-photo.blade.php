<div x-data="{ preview: null }">
    @php $user = auth()->user(); @endphp

    <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 4px;">Gambar Profil / Logo Syarikat</h3>
    <p style="font-size:13px;color:#6b7280;margin:0 0 16px;">JPG, PNG atau WEBP, maksimum 2MB. Gambar ini dipaparkan di sidebar.</p>

    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div style="width:96px;height:96px;border-radius:50%;overflow:hidden;background-color:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;flex-shrink:0;">
            <template x-if="preview"><img :src="preview" style="width:100%;height:100%;object-fit:cover;"></template>
            <template x-if="!preview">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                @endif
            </template>
        </div>

        <div>
            <input type="file" wire:model="photo" accept="image/*"
                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                   class="text-sm text-gray-600">
            @error('photo') <p style="color:#dc2626;font-size:12px;font-weight:bold;margin:6px 0 0;">⚠️ {{ $message }}</p> @enderror

            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="photo,save"
                        style="background-color:#1d4ed8;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                    <span wire:loading.remove wire:target="photo,save">Simpan Gambar</span>
                    <span wire:loading wire:target="photo,save">Memuat naik...</span>
                </button>
                @if ($user->avatar_path)
                    <button type="button" wire:click="remove" wire:confirm="Buang gambar profil?"
                            style="background-color:#fee2e2;color:#b91c1c;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                        Buang
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>