<div>
    @php
        $input = 'w-full border-gray-300 rounded-lg text-sm';
        $label = 'display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;';
        $err = 'color:#dc2626;font-size:12px;font-weight:bold;margin:4px 0 0;';
    @endphp

    <!-- ALAMAT SURAT MENYURAT -->
    <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 12px;">Alamat Surat Menyurat</h3>

    <textarea wire:model="mailing_address" rows="3" class="{{ $input }}" placeholder="No. unit, jalan, poskod, bandar, negeri"></textarea>
    @error('mailing_address') <p style="{{ $err }}">⚠️ {{ $message }}</p> @enderror

    <label style="display:flex;align-items:center;gap:8px;margin-top:12px;font-size:14px;color:#374151;cursor:pointer;">
        <input type="checkbox" wire:model.live="same" class="rounded border-gray-300">
        Alamat kurier sama dengan alamat surat menyurat
    </label>

    <div style="margin-top:12px;display:flex;align-items:center;gap:12px;">
        <button type="button" wire:click="saveMailing"
                style="background-color:#1d4ed8;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
            Simpan Alamat
        </button>
        @if ($mailingSaved)
            <span style="color:#059669;font-size:13px;font-weight:600;">✓ {{ $mailingSaved }}</span>
        @endif
    </div>

    <!-- ALAMAT KURIER -->
    <div style="margin-top:28px;border-top:1px solid #f3f4f6;padding-top:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0;">Alamat Kurier</h3>
            @unless ($same)
                <button type="button" wire:click="newAddress"
                        style="background-color:#059669;color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">
                    + Tambah Alamat
                </button>
            @endunless
        </div>

        @if ($same)
            <div style="padding:12px 14px;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;font-size:13px;color:#1e40af;">
                Alamat kurier menggunakan alamat surat menyurat di atas. Nyahtanda pilihan tersebut untuk urus alamat kurier berasingan.
            </div>
        @else
            @if ($showForm)
                <div style="padding:16px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:16px;display:flex;flex-direction:column;gap:12px;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                        <div>
                            <label style="{{ $label }}">Label *</label>
                            <input type="text" wire:model="label" placeholder="Pejabat / Rumah" class="{{ $input }}">
                            @error('label') <p style="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="{{ $label }}">Nama Penerima *</label>
                            <input type="text" wire:model="recipient_name" class="{{ $input }}">
                            @error('recipient_name') <p style="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="{{ $label }}">No. Telefon *</label>
                            <input type="text" wire:model="phone" class="{{ $input }}">
                            @error('phone') <p style="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">Alamat Penuh *</label>
                        <textarea wire:model="address" rows="3" class="{{ $input }}"></textarea>
                        @error('address') <p style="{{ $err }}">{{ $message }}</p> @enderror
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" wire:click="saveAddress"
                                style="background-color:#1d4ed8;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                            {{ $editingId ? 'Kemas Kini' : 'Simpan' }}
                        </button>
                        <button type="button" wire:click="cancelForm"
                                style="background-color:#e5e7eb;color:#374151;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                            Batal
                        </button>
                    </div>
                </div>
            @endif

            <div style="display:flex;flex-direction:column;gap:10px;">
                @forelse ($addresses as $a)
                    <div wire:key="addr-{{ $a->id }}" style="padding:14px;border:1px solid {{ $a->is_default ? '#93c5fd' : '#e5e7eb' }};border-radius:8px;background-color:{{ $a->is_default ? '#eff6ff' : '#ffffff' }};">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                            <div>
                                <p style="margin:0;font-size:14px;font-weight:700;color:#1f2937;">
                                    {{ $a->label }}
                                    @if ($a->is_default)
                                        <span style="font-size:10px;background-color:#1d4ed8;color:#fff;padding:2px 8px;border-radius:999px;margin-left:6px;">UTAMA</span>
                                    @endif
                                </p>
                                <p style="margin:4px 0 0;font-size:13px;color:#374151;">{{ $a->recipient_name }} · {{ $a->phone }}</p>
                                <p style="margin:2px 0 0;font-size:13px;color:#6b7280;white-space:pre-line;">{{ $a->address }}</p>
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @unless ($a->is_default)
                                    <button type="button" wire:click="setDefault({{ $a->id }})"
                                            style="background:none;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Jadikan Utama</button>
                                @endunless
                                <button type="button" wire:click="editAddress({{ $a->id }})"
                                        style="background:none;border:1px solid #d1d5db;color:#374151;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Edit</button>
                                <button type="button" wire:click="deleteAddress({{ $a->id }})" wire:confirm="Padam alamat ini?"
                                        style="background:none;border:1px solid #fecaca;color:#b91c1c;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Padam</button>
                            </div>
                        </div>
                    </div>
                @empty
                    @unless ($showForm)
                        <p style="font-size:13px;color:#6b7280;margin:0;">Belum ada alamat kurier. Klik <b>+ Tambah Alamat</b>.</p>
                    @endunless
                @endforelse
            </div>
        @endif
    </div>
</div>