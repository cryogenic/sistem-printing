<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Papan Kanban Pengeluaran Kilang
        </h2>
    </x-slot>

    <!-- Muat turun SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Grid 4 Lajur (Lajur Status) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                @php
                    $columns = [
                        'pending_artwork' => 'Menunggu Fail',
                        'in_printing' => 'Sedang Dicetak',
                        'finishing' => 'Proses Kemasan',
                        'ready_pickup' => 'Sedia Diambil'
                    ];
                @endphp

                @foreach($columns as $status_key => $column_title)
                <div class="bg-gray-100 rounded-lg p-4 shadow-inner min-h-[500px]">
                    <h3 class="font-bold text-gray-700 mb-3 border-b-2 border-gray-300 pb-2">{{ $column_title }}</h3>
                    
                    <!-- Container untuk kad yang boleh ditarik (Drag Container) -->
                    <div id="{{ $status_key }}" class="kanban-column space-y-3 min-h-[400px]">
                        
                        @if(isset($orders[$status_key]))
                            @foreach($orders[$status_key] as $order)
                            <!-- Kad Pesanan -->
                            <div class="bg-white p-4 rounded shadow border-l-4 border-indigo-500 cursor-move" data-id="{{ $order->id }}">
                                <p class="text-sm font-bold text-gray-800">{{ $order->order_no }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $order->customer }}</p>
                            </div>
                            @endforeach
                        @endif

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Skrip Drag & Drop AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const columns = document.querySelectorAll('.kanban-column');
            
            columns.forEach(column => {
                new Sortable(column, {
                    group: 'shared', // Membenarkan kad ditarik rentas lajur
                    animation: 150,
                    ghostClass: 'opacity-50', // Efek visual semasa ditarik
                    
                    // Fungsi berjalan apabila kad dilepaskan (Drop)
                    onEnd: function (evt) {
                        const orderId = evt.item.getAttribute('data-id');
                        const newStatus = evt.to.id; // ID lajur destinasi

                        // Jika kedudukan status berubah, hantar ke Backend
                        if (evt.from !== evt.to) {
                            fetch("{{ route('orders.kanban.update') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    order_id: orderId,
                                    new_status: newStatus
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(data.success) {
                                    console.log(data.message);
                                    // Pilihan: Tambah notifikasi Toast (seperti SweetAlert) di sini
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
</x-app-layout>