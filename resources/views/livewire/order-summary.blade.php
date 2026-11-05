<div>
    @php
        $mat       = $materials[$order->selected_material] ?? null;
        $isCourier = $order->fulfillment_type === 'courier';
        $shipping  = $isCourier ? 10 : 0;
        $subtotal  = $order->total_amount - $shipping;
        $area      = ($order->length && $order->width) ? $order->length * $order->width : null;
        $ext       = strtolower(pathinfo($order->artwork_path ?? '', PATHINFO_EXTENSION));
        $isImage   = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $isPaid    = $order->payment_status === 'paid';
        $card      = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $label     = 'font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:bold;margin:0;';
        $canWallet = $walletBalance >= $order->total_amount;
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <!-- KOLUM 1: SIDEBAR -->
                <x-sidebar />

                <!-- KOLUM 2: RUMUSAN -->
                <div x-data="{ showZoom: false }" style="flex:1;min-width:0;display:flex;flex-direction:column;gap:20px;">

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

                    <!-- Maklumat pesanan -->
                    <div style="{{ $card }}">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f3f4f6;padding-bottom:16px;margin-bottom:16px;">
                            <div>
                                <p style="{{ $label }}">No. Pesanan</p>
                                <p style="font-size:20px;font-weight:800;color:#2563eb;margin:2px 0 0;">{{ $order->order_number }}</p>
                            </div>
                            <span style="font-size:12px;font-weight:bold;padding:4px 12px;border-radius:999px;{{ $isPaid ? 'background-color:#d1fae5;color:#065f46;' : 'background-color:#fef3c7;color:#92400e;' }}">
                                {{ $isPaid ? '✓ Telah Dibayar' : 'Belum Dibayar' }}
                            </span>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;font-size:14px;">
                            <div style="grid-column:1/-1;">
                                <p style="{{ $label }}">Tajuk Tempahan</p>
                                <p style="font-weight:bold;color:#1f2937;margin:2px 0 0;">{{ $order->job_title }}</p>
                            </div>
                            <div>
                                <p style="{{ $label }}">Bahan Cetakan</p>
                                <p style="color:#1f2937;margin:2px 0 0;">{{ $mat['label'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p style="{{ $label }}">Saiz</p>
                                <p style="color:#1f2937;margin:2px 0 0;">
                                    @if ($area) {{ $order->length + 0 }} × {{ $order->width + 0 }} kaki ({{ number_format($area, 2) }} sqft) @else - @endif
                                </p>
                            </div>
                            <div>
                                <p style="{{ $label }}">Catatan</p>
                                <p style="color:#374151;margin:2px 0 0;">{{ $order->remarks ?: '-' }}</p>
                            </div>
                            <div>
                                <p style="{{ $label }}">Kaedah Penerimaan</p>
                                <p style="color:#1f2937;margin:2px 0 0;">{{ $isCourier ? 'Kurier' : 'Ambil Sendiri' }}</p>
                            </div>
                            @if ($isCourier)
                                <div style="grid-column:1/-1;">
                                    <p style="{{ $label }}">Alamat Penghantaran</p>
                                    <p style="color:#374151;margin:2px 0 0;">{{ $order->shipping_address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Artwork -->
                    <div style="{{ $card }}">
                        <p style="{{ $label }}margin-bottom:12px;">Artwork</p>
                        @if ($order->artwork_path)
                            @if ($isImage)
                                <img src="{{ asset('storage/' . $order->artwork_path) }}"
                                     @click="showZoom = true"
                                     style="max-width:100%;max-height:260px;object-fit:contain;border-radius:8px;border:2px solid #3b82f6;cursor:pointer;display:block;">
                                <button type="button" @click="showZoom = true"
                                        style="margin-top:8px;background:none;border:none;padding:0;color:#2563eb;font-size:13px;font-weight:600;cursor:pointer;">
                                    Klik untuk besarkan
                                </button>

                                <div x-show="showZoom" style="display:none;position:fixed;inset:0;z-index:50;background-color:rgba(0,0,0,0.75);align-items:center;justify-content:center;padding:16px;"
                                     :style="showZoom ? 'display:flex' : 'display:none'"
                                     @click.self="showZoom = false">
                                    <div style="position:relative;background:#fff;border-radius:12px;padding:8px;max-width:900px;max-height:90vh;overflow:auto;">
                                        <button type="button" @click="showZoom = false"
                                                style="position:absolute;top:12px;right:12px;background-color:#dc2626;color:#fff;border:none;border-radius:999px;width:32px;height:32px;cursor:pointer;">✕</button>
                                        <img src="{{ asset('storage/' . $order->artwork_path) }}" style="width:100%;height:auto;border-radius:8px;">
                                    </div>
                                </div>
                            @else
                                <a href="{{ asset('storage/' . $order->artwork_path) }}" target="_blank"
                                   style="color:#2563eb;font-weight:600;text-decoration:none;">📄 Buka fail artwork</a>
                            @endif
                        @else
                            <p style="font-size:14px;color:#6b7280;margin:0;">Tiada artwork dimuat naik.</p>
                        @endif
                    </div>

                    <!-- Ringkasan bayaran -->
                    <div style="{{ $card }}">
                        <p style="{{ $label }}margin-bottom:12px;">Ringkasan Bayaran</p>

                        <div style="font-size:14px;display:flex;flex-direction:column;gap:8px;">
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:#4b5563;">
                                    Cetakan @if ($mat && $area) ({{ number_format($area, 2) }} sqft × RM {{ number_format($mat['rate'], 2) }}) @endif
                                </span>
                                <span>RM {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:#4b5563;">Kurier</span>
                                <span>RM {{ number_format($shipping, 2) }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:10px;font-size:18px;font-weight:800;">
                                <span>Jumlah</span>
                                <span style="color:#d97706;">RM {{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>

                        @if (! $isPaid)
                            <div style="margin-top:24px;border-top:1px solid #f3f4f6;padding-top:20px;display:flex;flex-direction:column;gap:12px;">
                                <p style="{{ $label }}">Pilih Kaedah Pembayaran</p>

                                {{-- Wallet --}}
                                <button type="button" wire:click="payWithWallet"
                                        wire:confirm="Tolak RM {{ number_format($order->total_amount, 2) }} dari wallet anda?"
                                        wire:loading.attr="disabled" wire:target="payWithWallet"
                                        @disabled(! $canWallet)
                                        style="width:100%;display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-radius:8px;border:2px solid #1d4ed8;background-color:#ffffff;color:#1d4ed8;font-size:15px;font-weight:700;cursor:{{ $canWallet ? 'pointer' : 'not-allowed' }};opacity:{{ $canWallet ? '1' : '0.5' }};">
                                    <span>Bayar dengan Wallet</span>
                                    <span style="font-size:13px;">Baki: RM {{ number_format($walletBalance, 2) }}</span>
                                </button>
                                @if (! $canWallet)
    <p style="font-size:12px;color:#dc2626;margin:0;">
        Baki tidak mencukupi.
        <a href="{{ route('wallet.topup') }}" style="color:#1d4ed8;font-weight:700;">Top up wallet &rarr;</a>
    </p>
@endif

                                {{-- FPX --}}
                                <button type="button" wire:click="payWithFpx"
                                        wire:loading.attr="disabled" wire:target="payWithFpx"
                                        style="width:100%;padding:14px 16px;border-radius:8px;border:none;background-color:#1d4ed8;color:#ffffff;font-size:15px;font-weight:700;cursor:pointer;">
                                    <span wire:loading.remove wire:target="payWithFpx">Bayar dengan FPX (ToyyibPay)</span>
                                    <span wire:loading wire:target="payWithFpx">Menyambung ke ToyyibPay...</span>
                                </button>

                                {{-- Ralat dipaparkan di sini, dekat butang --}}
                                @if ($errorMessage)
                                    <div style="padding:12px 14px;background-color:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:8px;font-size:13px;font-weight:600;">
                                        ⚠️ {{ $errorMessage }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div style="margin-top:24px;">
                            <a href="{{ route('orders.list') }}" style="color:#2563eb;font-weight:600;text-decoration:none;font-size:14px;">&larr; Kembali ke Senarai</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>