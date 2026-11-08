<div>
    @php
        $card = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $label = 'display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;';
        $err = 'color:#dc2626;font-size:12px;font-weight:bold;margin:4px 0 0;';
        $btn = 'background-color:#1d4ed8;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;';
        $th = 'text-align:left;padding:10px 12px;font-size:12px;font-weight:600;color:#374151;background-color:#f3f4f6;border-bottom:1px solid #e5e7eb;';
        $td = 'padding:10px 12px;font-size:13px;border-bottom:1px solid #f3f4f6;color:#374151;';
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <x-sidebar />

                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:20px;">

                    @if ($tempPassword)
                        <div style="padding:16px;background-color:#fef3c7;border:1px solid #fcd34d;border-radius:8px;">
                            <p style="margin:0;font-size:14px;font-weight:700;color:#92400e;">Pelanggan didaftarkan: {{ $createdEmail }}</p>
                            <p style="margin:6px 0;font-size:13px;color:#92400e;">Kata laluan sementara (dipaparkan sekali sahaja, salin sekarang):</p>
                            <code style="font-size:18px;font-weight:700;background:#fff;padding:6px 12px;border-radius:6px;user-select:all;">{{ $tempPassword }}</code>
                            <div style="margin-top:10px;">
                                <button type="button" wire:click="dismissPassword" style="{{ $btn }}">Saya sudah salin</button>
                            </div>
                        </div>
                    @endif

                    @if ($message)
                        <div style="padding:12px 16px;background-color:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;font-size:14px;font-weight:600;">
                            ✅ {{ $message }}
                        </div>
                    @endif

                    <div style="{{ $card }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
                            <h2 class="text-xl font-bold text-gray-800" style="margin:0;">Pelanggan</h2>
                            <div style="display:flex;gap:10px;">
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / e-mel / telefon" class="border-gray-300 rounded-lg text-sm" style="width:240px;">
                                <button type="button" wire:click="$toggle('showForm')"
                                        style="background-color:#059669;color:#fff;border:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                                    + Daftar Pelanggan
                                </button>
                            </div>
                        </div>

                        @if ($showForm)
                            <div style="padding:16px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:16px;">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                                    <div>
                                        <label style="{{ $label }}">Nama *</label>
                                        <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg text-sm">
                                        @error('name') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label style="{{ $label }}">E-mel *</label>
                                        <input type="email" wire:model="email" class="w-full border-gray-300 rounded-lg text-sm">
                                        @error('email') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label style="{{ $label }}">Telefon</label>
                                        <input type="text" wire:model="phone" class="w-full border-gray-300 rounded-lg text-sm">
                                        @error('phone') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div style="margin-top:12px;">
                                    <button type="button" wire:click="createCustomer" style="{{ $btn }}">Daftar</button>
                                </div>
                            </div>
                        @endif

                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="{{ $th }}">Pelanggan</th>
                                        <th style="{{ $th }}">Telefon</th>
                                        <th style="{{ $th }}text-align:right;">Order</th>
                                        <th style="{{ $th }}text-align:right;">Baki (RM)</th>
                                        <th style="{{ $th }}"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($customers as $c)
                                        <tr wire:key="cust-{{ $c->id }}">
                                            <td style="{{ $td }}">
                                                <b>{{ $c->name }}</b><br>
                                                <span style="font-size:12px;color:#9ca3af;">{{ $c->email }}</span>
                                            </td>
                                            <td style="{{ $td }}">{{ $c->phone ?: '-' }}</td>
                                            <td style="{{ $td }}text-align:right;">{{ $c->orders_count }}</td>
                                            <td style="{{ $td }}text-align:right;font-weight:700;">{{ number_format($c->wallet->balance ?? 0, 2) }}</td>
                                            <td style="{{ $td }}text-align:right;">
                                                <button type="button" wire:click="openAdjust({{ $c->id }})"
                                                        style="background:none;border:1px solid #bfdbfe;color:#1d4ed8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                                    Laras Baki
                                                </button>
                                            </td>
                                        </tr>

                                        @if ($adjustingId === $c->id)
                                            <tr wire:key="adj-{{ $c->id }}">
                                                <td colspan="5" style="padding:14px;background-color:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                                                        <div>
                                                            <label style="{{ $label }}">Jenis</label>
                                                            <select wire:model="direction" class="border-gray-300 rounded-lg text-sm">
                                                                <option value="credit">Tambah (+)</option>
                                                                <option value="debit">Tolak (-)</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label style="{{ $label }}">Jumlah (RM)</label>
                                                            <input type="number" step="0.01" wire:model="amount" class="border-gray-300 rounded-lg text-sm" style="width:130px;">
                                                            @error('amount') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                                        </div>
                                                        <div style="flex:1;min-width:200px;">
                                                            <label style="{{ $label }}">Sebab *</label>
                                                            <input type="text" wire:model="reason" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Contoh: bayaran tunai di kaunter">
                                                            @error('reason') <p style="{{ $err }}">{{ $message }}</p> @enderror
                                                        </div>
                                                        <button type="button" wire:click="saveAdjust" style="{{ $btn }}">Simpan</button>
                                                        <button type="button" wire:click="$set('adjustingId', null)"
                                                                style="background-color:#e5e7eb;color:#374151;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                                                            Batal
                                                        </button>
                                                    </div>
                                                    @if ($error)
                                                        <p style="{{ $err }}">⚠️ {{ $error }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="5" style="{{ $td }}text-align:center;color:#6b7280;padding:24px;">Tiada pelanggan dijumpai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>