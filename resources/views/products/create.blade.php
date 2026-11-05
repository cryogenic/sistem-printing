<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Produk / Material Baharu
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nama Produk -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Material (Contoh: Tarpaulin 380gsm)</label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Jenis Harga -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jenis Pengiraan</label>
                        <select name="pricing_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="per_sqft">Kiraan Kaki Persegi (Per Sqft)</option>
                            <option value="per_piece">Kiraan Keping (Per Piece)</option>
                        </select>
                    </div>

                    <!-- Harga Asas -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Harga Asas (RM)</label>
                        <input type="number" step="0.01" name="base_price" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Simpan Produk
                    </button>
                    <a href="{{ route('products.index') }}" class="ml-2 text-gray-600 hover:text-gray-900">Batal</a>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>