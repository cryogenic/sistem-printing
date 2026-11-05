<div>
    @php
        $card = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $label = 'display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;';
        $err = 'color:#dc2626;font-size:12px;font-weight:bold;margin:4px 0 0;';
        $btn = 'background-color:#1d4ed8;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;';
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <x-sidebar />

                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:20px;">

                    <!-- Tetapan am -->
                    <div style="{{ $card }}">
                        <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 16px;">Tetapan Kedai</h3>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                            <div>
                                <label style="{{ $label }}">Nama Kedai / Sistem *</label>
                                <input type="text" wire:model="shop_name" class="w-full border-gray-300 rounded-lg text-sm">
                                @error('shop_name') <p style="{{ $err }}">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label style="{{ $label }}">Tagline</label>
                                <input type="text" wire:model="shop_tagline" class="w-full border-gray-300 rounded-lg text-sm">
                                @error('shop_tagline') <p style="{{ $err }}">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label style="{{ $label }}">Kos Kurier (RM) *</label>
                                <input type="number" step="0.01" min="0" wire:model="courier_fee" class="w-full border-gray-300 rounded-lg text-sm">
                                @error('courier_fee') <p style="{{ $err }}">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <label style="display:flex;align-items:center;gap:8px;margin-top:16px;font-size:14px;color:#374151;cursor:pointer;">
                            <input type="checkbox" wire:model="allow_registration" class="rounded border-gray-300">
                            Benarkan pelanggan daftar sendiri
                        </label>

                        <div style="margin-top:16px;display:flex;align-items:center;gap:12px;">
                            <button type="button" wire:click="saveSettings" style="{{ $btn }}">Simpan Tetapan</button>
                            @if ($saved) <span style="color:#059669;font-size:13px;font-weight:600;">✓ {{ $saved }}</span> @endif
                        </div>
                    </div>

                    <!-- Bahan cetakan -->
                    <div style="{{ $card }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0;">Bahan Cetakan</h3>
                            <button type="button" wire:click="newMaterial"
                                    style="background-color:#059669;color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">
                                + Tambah Bahan
                            </button>
                        </div>

                        @if ($showForm)
                            <div style="padding:16px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:16px;">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                                    <div>
                                        <label style="{{ $label }}">Nama *</label>
                                        <input type="text" wire:model="m_name" class="w-full border-gray-300 rounded-lg text-sm">
                                        @error('m_name') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label style="{{ $label }}">Kod *</label>
                                        <input type="text" wire:model="m_code" placeholder="contoh: vinyl_matt" class="w-full border-gray-300 rounded-lg text-sm" @if($editingId) readonly @endif>
                                        @error('m_code') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label style="{{ $label }}">Harga / sqft (RM) *</label>
                                        <input type="number" step="0.01" wire:model="m_price" class="w-full border-gray-300 rounded-lg text-sm">
                                        @error('m_price') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div style="margin-top:12px;display:flex;gap:8px;">
                                    <button type="button" wire:click="saveMaterial" style="{{ $btn }}">{{ $editingId ? 'Kemas Kini' : 'Simpan' }}</button>
                                    <button type="button" wire:click="$set('showForm', false)" style="background-color:#e5e7eb;color:#374151;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Batal</button>
                                </div>
                            </div>
                        @endif

                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;">
                                <thead><tr>
                                    @foreach (['Nama', 'Kod', 'Harga / sqft', 'Status', ''] as $h)
                                        <th style="text-align:left;padding:10px 12px;font-size:12px;font-weight:600;color:#374151;background-color:#f3f4f6;border-bottom:1px solid #e5e7eb;">{{ $h }}</th>
                                    @endforeach
                                </tr></thead>
                                <tbody>
                                @foreach ($materials as $m)
                                    <tr wire:key="mat-{{ $m->id }}">
                                        <td style="padding:10px 12px;font-size:13px;border-bottom:1px solid #f3f4f6;font-weight:600;">{{ $m->name }}</td>
                                        <td style="padding:10px 12px;font-size:12px;border-bottom:1px solid #f3f4f6;color:#6b7280;">{{ $m->code }}</td>
                                        <td style="padding:10px 12px;font-size:13px;border-bottom:1px solid #f3f4f6;">RM {{ number_format($m->price_per_sqft, 2) }}</td>
                                        <td style="padding:10px 12px;font-size:12px;border-bottom:1px solid #f3f4f6;">
                                            <span style="font-weight:700;color:{{ $m->is_active ? '#059669' : '#9ca3af' }};">{{ $m->is_active ? 'Aktif' : 'Tidak aktif' }}</span>
                                        </td>
                                        <td style="padding:10px 12px;border-bottom:1px solid #f3f4f6;text-align:right;white-space:nowrap;">
                                            <button type="button" wire:click="editMaterial({{ $m->id }})" style="background:none;border:1px solid #d1d5db;color:#374151;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Edit</button>
                                            <button type="button" wire:click="toggleMaterial({{ $m->id }})" style="background:none;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">{{ $m->is_active ? 'Nyahaktif' : 'Aktifkan' }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p style="font-size:12px;color:#6b7280;margin:12px 0 0;">Bahan yang dinyahaktifkan tidak muncul dalam kalkulator, tetapi order lama kekal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>