<x-app-layout>
    @php
        $statusLabels = [
            'pending'         => 'Menunggu',
            'pending_artwork' => 'Pending Artwork',
            'processing'      => 'Diproses',
            'printing'        => 'Sedang Dicetak',
            'ready'           => 'Sedia Diambil',
            'completed'       => 'Selesai',
        ];
        $card = 'background-color:#ffffff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);';
        $statLabel = 'font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:bold;margin:0;';
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <!-- KOLUM 1: SIDEBAR -->
                <x-sidebar />

                <!-- KOLUM 2: KANDUNGAN -->
                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:24px;">

                    <!-- Kad ringkasan (klik untuk tapis senarai) -->
                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                        <a href="{{ route('orders.list') }}"
                           style="{{ $card }}flex:1;min-width:160px;border-left:4px solid #3b82f6;text-decoration:none;display:block;">
                            <p style="{{ $statLabel }}">Jumlah Order</p>
                            <p style="font-size:32px;font-weight:800;color:#111827;margin:4px 0 0;">{{ $stats['total'] }}</p>
                        </a>
                        <a href="{{ route('orders.list', ['payment' => 'unpaid']) }}"
                           style="{{ $card }}flex:1;min-width:160px;border-left:4px solid #f59e0b;text-decoration:none;display:block;">
                            <p style="{{ $statLabel }}">Belum Bayar</p>
                            <p style="font-size:32px;font-weight:800;color:#d97706;margin:4px 0 0;">{{ $stats['unpaid'] }}</p>
                        </a>
                        <a href="{{ route('orders.list', ['payment' => 'paid']) }}"
                           style="{{ $card }}flex:1;min-width:160px;border-left:4px solid #10b981;text-decoration:none;display:block;">
                            <p style="{{ $statLabel }}">Sudah Bayar</p>
                            <p style="font-size:32px;font-weight:800;color:#059669;margin:4px 0 0;">{{ $stats['paid'] }}</p>
                        </a>
                    </div>

                    <!-- Chart -->
                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                        <div style="{{ $card }}flex:1;min-width:280px;">
                            <p style="font-size:14px;font-weight:bold;color:#1f2937;margin:0 0 12px;">Order (6 Bulan)</p>
                            <div style="position:relative;height:220px;"><canvas id="ordersChart"></canvas></div>
                        </div>
                        <div style="{{ $card }}flex:1;min-width:280px;">
                            <p style="font-size:14px;font-weight:bold;color:#1f2937;margin:0 0 12px;">Bayaran Diterima (RM)</p>
                            <div style="position:relative;height:220px;"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>

                    <!-- Kanban status order -->
                    <div style="{{ $card }}">
                        <p style="font-size:14px;font-weight:bold;color:#1f2937;margin:0 0 16px;">Status Order</p>

                        @if ($byStatus->isEmpty())
                            <p style="font-size:14px;color:#6b7280;margin:0;">Belum ada order. Klik <b>+ New Order</b> untuk mula.</p>
                        @else
                            <div style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
                                @foreach ($byStatus as $status => $items)
                                    <div style="min-width:230px;flex:1;background-color:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;padding:12px;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                            <a href="{{ route('orders.list', ['status' => $status]) }}"
                                               style="font-size:11px;font-weight:bold;text-transform:uppercase;color:#374151;text-decoration:none;">
                                                {{ $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status)) }}
                                            </a>
                                            <span style="font-size:11px;font-weight:bold;background-color:#e5e7eb;border-radius:999px;padding:2px 8px;">{{ $items->count() }}</span>
                                        </div>

                                        <div style="display:flex;flex-direction:column;gap:8px;">
                                            @foreach ($items->take(5) as $o)
                                                <a href="{{ route('order.summary', $o->order_number) }}"
                                                   style="display:block;background-color:#ffffff;border-radius:8px;border:1px solid #e5e7eb;padding:10px;text-decoration:none;">
                                                    <p style="font-size:11px;font-weight:bold;color:#2563eb;margin:0;">{{ $o->order_number }}</p>
                                                    <p style="font-size:14px;font-weight:600;color:#1f2937;margin:2px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $o->job_title }}</p>
                                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                                        <span style="font-size:12px;color:#6b7280;">RM {{ number_format($o->total_amount, 2) }}</span>
                                                        <span style="font-size:10px;font-weight:bold;padding:2px 8px;border-radius:999px;{{ $o->payment_status === 'paid' ? 'background-color:#d1fae5;color:#065f46;' : 'background-color:#fef3c7;color:#92400e;' }}">
                                                            {{ $o->payment_status === 'paid' ? 'Dibayar' : 'Belum Bayar' }}
                                                        </span>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @if ($items->count() > 5)
                                                <a href="{{ route('orders.list', ['status' => $status]) }}"
                                                   style="text-align:center;font-size:12px;color:#2563eb;font-weight:600;text-decoration:none;">
                                                    +{{ $items->count() - 5 }} lagi
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
                <!-- /KOLUM 2 -->

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($labels);

        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Order', data: @json($orders), backgroundColor: '#3b82f6', borderRadius: 4 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'RM', data: @json($revenue), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.15)', fill: true, tension: 0.3 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</x-app-layout>