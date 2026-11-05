<div>
    @php
        $card = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $th = 'text-align:left;padding:10px 12px;font-size:12px;font-weight:600;color:#374151;background-color:#f3f4f6;border-bottom:1px solid #e5e7eb;';
        $td = 'padding:10px 12px;font-size:13px;border-bottom:1px solid #f3f4f6;color:#374151;';
        $tabStyle = fn ($on) => 'padding:8px 16px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;border:none;'
            . ($on ? 'background-color:#1d4ed8;color:#ffffff;' : 'background-color:#e5e7eb;color:#374151;');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <x-sidebar />

                <div style="flex:1;min-width:0;{{ $card }}">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                        <h2 class="text-2xl font-bold text-gray-800" style="margin:0;">Sejarah Bil</h2>
                        <div style="display:flex;gap:8px;">
                            <button type="button" wire:click="setTab('topup')" style="{{ $tabStyle($tab === 'topup') }}">Top-Up</button>
                            <button type="button" wire:click="setTab('payments')" style="{{ $tabStyle($tab === 'payments') }}">Pembayaran</button>
                        </div>
                    </div>

                    <div style="overflow-x:auto;">
                    @if ($tab === 'topup')
                        <table style="width:100%;border-collapse:collapse;">
                            <thead><tr>
                                <th style="{{ $th }}">Tarikh</th>
                                <th style="{{ $th }}">Rujukan</th>
                                <th style="{{ $th }}">Status</th>
                                <th style="{{ $th }}text-align:right;">Jumlah (RM)</th>
                            </tr></thead>
                            <tbody>
                            @forelse ($topups as $t)
                                <tr wire:key="topup-{{ $t->id }}">
                                    <td style="{{ $td }}">{{ $t->created_at->format('d/m/Y h:i A') }}</td>
                                    <td style="{{ $td }}">{{ $t->payment_reference ?: 'TOPUP-' . $t->id }}</td>
                                    <td style="{{ $td }}">
                                        @if ($t->status === 'paid')
                                            <span style="color:#059669;font-weight:700;">Berjaya</span>
                                        @else
                                            <span style="color:#d97706;font-weight:700;">Belum selesai</span>
                                        @endif
                                    </td>
                                    <td style="{{ $td }}text-align:right;font-weight:700;color:#059669;">+ {{ number_format($t->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="{{ $td }}text-align:center;color:#6b7280;padding:24px;">Belum ada top-up.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    @else
                        <table style="width:100%;border-collapse:collapse;">
                            <thead><tr>
                                <th style="{{ $th }}">Tarikh</th>
                                <th style="{{ $th }}">No. Pesanan</th>
                                <th style="{{ $th }}">Kaedah</th>
                                <th style="{{ $th }}">Rujukan</th>
                                <th style="{{ $th }}text-align:right;">Jumlah (RM)</th>
                            </tr></thead>
                            <tbody>
                            @forelse ($payments as $p)
                                <tr wire:key="pay-{{ $p->id }}">
                                    <td style="{{ $td }}">{{ $p->updated_at->format('d/m/Y h:i A') }}</td>
                                    <td style="{{ $td }}">
                                        <a href="{{ route('order.summary', $p->order_number) }}" style="color:#4f46e5;text-decoration:none;font-weight:600;">{{ $p->order_number }}</a>
                                    </td>
                                    <td style="{{ $td }}">{{ $p->payment_method === 'wallet' ? 'Wallet' : 'FPX' }}</td>
                                    <td style="{{ $td }}">{{ $p->payment_reference ?: '-' }}</td>
                                    <td style="{{ $td }}text-align:right;font-weight:700;color:#dc2626;">- {{ number_format($p->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="{{ $td }}text-align:center;color:#6b7280;padding:24px;">Belum ada pembayaran.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>