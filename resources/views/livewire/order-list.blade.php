<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <!-- KOLUM 1: SIDEBAR -->
                <x-sidebar />

                <!-- KOLUM 2: SENARAI TEMPAHAN -->
                <div style="flex-grow:1;min-width:0;background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);">

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                        <h2 class="text-2xl font-bold text-gray-800" style="margin:0;">Senarai Tempahan Saya</h2>
                        <a href="{{ route('printing.calculator') }}"
                           style="background-color:#1d4ed8;color:#ffffff;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                            + New Order
                        </a>
                    </div>

                    @if (session()->has('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- BAR PENAPIS & SUSUNAN -->
<div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;padding:12px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
    <div>
        <label style="display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;">Pembayaran</label>
        <select wire:model.live="paymentFilter" class="border-gray-300 rounded-lg text-sm">
            <option value="">Semua</option>
            <option value="unpaid">Belum Dibayar</option>
            <option value="paid">Telah Dibayar</option>
        </select>
    </div>

    <div>
        <label style="display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;">Status Order</label>
        <select wire:model.live="statusFilter" class="border-gray-300 rounded-lg text-sm">
            <option value="">Semua</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}">{{ ucwords(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label style="display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;">Dari Tarikh</label>
        <input type="date" wire:model.live="dateFrom" class="border-gray-300 rounded-lg text-sm">
    </div>

    <div>
        <label style="display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;">Hingga Tarikh</label>
        <input type="date" wire:model.live="dateTo" class="border-gray-300 rounded-lg text-sm">
    </div>

    <div>
        <label style="display:block;font-size:11px;font-weight:bold;color:#6b7280;text-transform:uppercase;margin-bottom:4px;">Susunan Tarikh</label>
        <select wire:model.live="sortDate" class="border-gray-300 rounded-lg text-sm">
            <option value="newest">Terbaru dahulu</option>
            <option value="oldest">Terlama dahulu</option>
        </select>
    </div>

    @if ($paymentFilter !== '' || $statusFilter !== '' || $sortDate !== 'newest' || $dateFrom !== '' || $dateTo !== '')
        <button type="button" wire:click="resetFilters"
                style="background-color:#e5e7eb;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
            Reset
        </button>
    @endif

    <span style="margin-left:auto;font-size:13px;color:#6b7280;">{{ $orders->count() }} order</span>
</div>

                    <div style="overflow-x:auto;">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">No. Pesanan</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">
                                        <button type="button"
                                                wire:click="$set('sortDate', '{{ $sortDate === 'newest' ? 'oldest' : 'newest' }}')"
                                                style="background:none;border:none;padding:0;font:inherit;font-weight:600;color:inherit;cursor:pointer;">
                                            Tarikh {{ $sortDate === 'newest' ? '↓' : '↑' }}
                                        </button>
                                    </th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Jumlah (RM)</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50 transition duration-150" wire:key="order-{{ $order->id }}">
                                        <td class="py-3 px-4 border-b text-sm font-medium">
                                            <a href="{{ route('order.summary', $order->order_number) }}"
                                               class="text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="py-3 px-4 border-b text-sm text-gray-600">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                                        <td class="py-3 px-4 border-b text-sm text-gray-800 font-bold">{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="py-3 px-4 border-b text-sm">
                                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold mb-1 inline-block">
                                                {{ ucwords(str_replace('_', ' ', $order->production_status)) }}
                                            </span><br>
                                            @if($order->payment_status === 'paid')
                                                <span class="text-xs text-green-600 font-bold">Telah Dibayar</span>
                                            @else
                                                <span class="text-xs text-red-600 font-bold">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b text-sm">
                                            <div style="display:flex;flex-direction:column;gap:8px;">

                                                {{-- Pautan bayar untuk order belum dibayar --}}
                                                @if($order->payment_status !== 'paid')
                                                    <a href="{{ route('order.summary', $order->order_number) }}"
                                                       style="background-color:#1d4ed8;color:#ffffff;padding:4px 12px;border-radius:4px;font-size:12px;font-weight:bold;text-decoration:none;text-align:center;">
                                                        Bayar Sekarang
                                                    </a>
                                                @endif

                                                {{-- Invois PDF untuk order yang telah dibayar --}}
                                                @if($order->payment_status === 'paid')
                                                    <a href="{{ route('invoice.download', $order->id) }}"
                                                       style="background-color:#059669;color:#ffffff;padding:4px 12px;border-radius:4px;font-size:12px;font-weight:bold;text-decoration:none;text-align:center;">
                                                        Invois PDF
                                                    </a>
                                                @endif

                                                {{-- Artwork --}}
                                                @if($order->artwork_path)
                                                    <span class="text-green-600 font-semibold">✅ Artwork Dihantar</span>
                                                @else
                                                    @if($uploading_order_id == $order->id)
                                                        <div class="flex flex-col gap-2">
                                                            <input type="file" wire:model="artwork" class="text-xs border p-1 rounded w-full">
                                                            <div wire:loading wire:target="artwork" class="text-xs text-indigo-500 font-bold mt-1">Sistem sedang memuat naik, sila tunggu sebentar...</div>
                                                            @error('artwork') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror

                                                            <div style="display:flex;gap:8px;margin-top:8px;">
                                                                <button type="button" wire:click="saveArtwork" style="background-color:#4f46e5;color:white;padding:6px 12px;border-radius:4px;font-size:12px;font-weight:bold;border:none;cursor:pointer;">
                                                                    Simpan
                                                                </button>
                                                                <button type="button" wire:click="$set('uploading_order_id', null)" style="background-color:#9ca3af;color:white;padding:6px 12px;border-radius:4px;font-size:12px;font-weight:bold;border:none;cursor:pointer;">
                                                                    Batal
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <button type="button" wire:click="setUploadOrder({{ $order->id }})" style="background-color:#e0e7ff;color:#4338ca;padding:4px 12px;border-radius:4px;font-size:12px;font-weight:bold;border:1px solid #a5b4fc;cursor:pointer;">
                                                            Muat Naik Artwork
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-500">Tiada rekod tempahan dijumpai.</td>
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