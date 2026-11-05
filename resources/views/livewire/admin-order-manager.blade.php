<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <!-- KOLUM 1: SIDEBAR -->
                <x-sidebar />

                <!-- KOLUM 2: PANEL ADMIN -->
                <div style="flex:1;min-width:0;background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);">

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Papan Kawalan Pentadbir (Admin Panel)</h2>
                            <p class="text-xs text-gray-500">Pengurusan Pesanan & Status Percetakan Kilang</p>
                        </div>
                        <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-xs">Akses Admin</span>
                    </div>

                    @if (session()->has('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md text-sm font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div style="overflow-x:auto;">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-xs font-bold text-gray-700 uppercase">No. Pesanan</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-bold text-gray-700 uppercase">Pelanggan</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-bold text-gray-700 uppercase">Jumlah (RM)</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-bold text-gray-700 uppercase">Artwork</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-bold text-gray-700 uppercase">Kemas Kini Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition border-b" wire:key="admin-order-{{ $order->id }}">
                                    <td class="py-3 px-4 text-sm font-bold">
                                        <a href="{{ route('order.summary', $order->order_number) }}" class="text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-800 font-medium">
                                        {{ $order->user->name ?? 'N/A' }}<br>
                                        <span class="text-xs text-gray-400">{{ $order->user->email ?? '' }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm font-bold text-gray-900">
                                        RM {{ number_format($order->total_amount, 2) }}<br>
                                        @if($order->payment_status === 'paid')
                                            <span class="text-xs text-green-600 font-bold">Telah Dibayar</span>
                                        @else
                                            <span class="text-xs text-red-600 font-bold">Belum Dibayar</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm">
                                        @if($order->artwork_path)
                                            <button type="button" wire:click="downloadArtwork({{ $order->id }})" style="background-color:#059669;color:white;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:bold;border:none;cursor:pointer;">
                                                📥 Muat Turun
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Ada Fail</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm">
                                        <select wire:change="updateStatus({{ $order->id }}, $event.target.value)" class="text-xs border p-1.5 rounded font-bold text-gray-700 bg-gray-50">
                                            <option value="pending_artwork" {{ $order->production_status == 'pending_artwork' ? 'selected' : '' }}>Pending Artwork</option>
                                            <option value="processing" {{ $order->production_status == 'processing' ? 'selected' : '' }}>Processing (Dalam Cetakan)</option>
                                            <option value="ready_for_collection" {{ $order->production_status == 'ready_for_collection' ? 'selected' : '' }}>Ready for Collection</option>
                                            <option value="completed" {{ $order->production_status == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Tiada sebarang pesanan dijumpai dalam sistem.</td>
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