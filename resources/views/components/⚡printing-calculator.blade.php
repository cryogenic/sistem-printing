<div class="p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Kalkulator Cetakan Banner</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Input Panjang -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Panjang (Kaki)</label>
            <input type="number" wire:model.live="length" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Input Lebar -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Lebar (Kaki)</label>
            <input type="number" wire:model.live="width" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <!-- Pilihan Kemasan (Finishing) -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Kemasan</label>
        <select wire:model.live="finishing_cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="0">Tiada (Potong Ikut Saiz)</option>
            <option value="2.00">Eyelets / Lubang Cincin (+ RM2.00)</option>
            <option value="5.00">PVC Pipe Atas & Bawah (+ RM5.00)</option>
            <option value="8.00">Kayu Atas & Bawah (+ RM8.00)</option>
        </select>
    </div>

    <!-- Paparan Harga Serta-merta -->
    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-md">
        <p class="text-sm text-gray-600">Jumlah Harga Anggaran:</p>
        <p class="text-3xl font-bold text-indigo-600">RM {{ number_format($total_price, 2) }}</p>
    </div>

    <!-- Butang Tempah -->
    <button class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded">
        Teruskan Tempahan
    </button>
</div>