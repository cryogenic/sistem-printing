<div>
    @php
        $card = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $label = 'font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:bold;margin:0 0 8px;';
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <x-sidebar />

                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:20px;">

                    @if (session()->has('success'))
                        <div style="padding:14px 16px;background-color:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;font-size:14px;font-weight:600;">
                            ✅ {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div style="padding:14px 16px;background-color:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;font-size:14px;font-weight:600;">
                            ⚠️ {{ session('error') }}
                        </div>
                    @endif

                    <!-- Baki -->
                    <div style="{{ $card }}background-color:#0f172a;">
                        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:bold;margin:0;">Baki Wallet</p>
                        <p style="font-size:36px;font-weight:800;color:#fbbf24;margin:4px 0 0;">RM {{ number_format($balance, 2) }}</p>
                    </div>

                    <!-- Top up -->
                    <div style="{{ $card }}">
                        <p style="{{ $label }}">Pilih Jumlah Top-Up</p>

                        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                            @foreach ([10, 20, 50, 100] as $preset)
                                <button type="button" wire:click="setAmount({{ $preset }})"
                                        style="padding:10px 20px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;
                                        {{ (int) $amount === $preset
                                            ? 'background-color:#1d4ed8;color:#ffffff;border:2px solid #1d4ed8;'
                                            : 'background-color:#ffffff;color:#1d4ed8;border:2px solid #bfdbfe;' }}">
                                    RM {{ $preset }}
                                </button>
                            @endforeach
                        </div>

                        <p style="{{ $label }}">Atau Jumlah Lain (RM 5 - RM 1,000)</p>
                        <input type="number" min="5" max="1000" step="1" wire:model.live="amount"
                               class="border-gray-300 rounded-lg text-sm" style="width:200px;">
                        @error('amount')
                            <p style="color:#dc2626;font-size:12px;font-weight:bold;margin:6px 0 0;">⚠️ {{ $message }}</p>
                        @enderror

                        <div style="margin-top:20px;">
                            <button type="button" wire:click="topup" wire:loading.attr="disabled" wire:target="topup"
                                    style="width:100%;padding:14px 16px;border-radius:8px;border:none;background-color:#1d4ed8;color:#ffffff;font-size:15px;font-weight:700;cursor:pointer;">
                                <span wire:loading.remove wire:target="topup">Top Up RM {{ is_numeric($amount) ? number_format($amount, 2) : '0.00' }} dengan FPX</span>
                                <span wire:loading wire:target="topup">Menyambung ke ToyyibPay...</span>
                            </button>
                        </div>

                        @if ($errorMessage)
                            <div style="margin-top:12px;padding:12px 14px;background-color:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;font-size:13px;font-weight:600;">
                                ⚠️ {{ $errorMessage }}
                            </div>
                        @endif
                    </div>

                    <!-- Sejarah -->
                    <div style="{{ $card }}">
                        <p style="{{ $label }}">Top-Up Terkini</p>
                        @forelse ($history as $h)
                            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:14px;">
                                <span style="color:#4b5563;">{{ $h->created_at->format('d/m/Y h:i A') }}</span>
                                <span style="font-weight:700;color:#059669;">+ RM {{ number_format($h->amount, 2) }}</span>
                            </div>
                        @empty
                            <p style="font-size:14px;color:#6b7280;margin:0;">Belum ada top-up.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>