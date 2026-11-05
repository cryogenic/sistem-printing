<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div style="display:flex;gap:24px;align-items:flex-start;">

            <!-- KOLUM 1: SIDEBAR -->
            <x-sidebar />

            <!-- KOLUM 2: BORANG -->
            <div style="flex:1;min-width:0;background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);">

                <div x-data="{ showZoomModal: false }" class="space-y-6">
                    <div class="border-b border-gray-200 pb-3">
                        <h2 class="text-xl font-bold text-gray-900">Borang Tempahan Percetakan Baharu</h2>
                        <p class="text-xs text-gray-500">Sila isi butiran spesifikasi cetakan dan muat naik fail rekaan anda di bawah.</p>
                    </div>

                    @if (session()->has('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-semibold shadow-sm">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="submitOrder" class="space-y-6">

                        @if ($errors->any())
                            <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                                <ul class="list-disc ml-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- 1. TAJUK TEMPAHAN -->
                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Tajuk Tempahan / Job Title *</label>
                            <input type="text" wire:model="job_title" placeholder="Contoh: Bunting Event Perkim" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            @error('job_title') <span class="text-red-600 text-xs font-bold mt-1 block">⚠️ {{ $message }}</span> @enderror
                        </div>

                        <!-- 2. KALKULATOR -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Bahan Cetakan</label>
                                <select wire:model.live="selected_material" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                    @foreach ($materials as $mat)
                                        <option value="{{ $mat->code }}">{{ $mat->name }} (RM {{ number_format($mat->price_per_sqft, 2) }} / Sqft)</option>
                                    @endforeach
                                </select>
                                @error('selected_material') <span class="text-red-600 text-xs font-bold mt-1 block">⚠️ {{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Panjang (Kaki) *</label>
                                <input type="number" step="0.1" wire:model.live="length" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Lebar (Kaki) *</label>
                                <input type="number" step="0.1" wire:model.live="width" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            </div>
                        </div>

                        <!-- 3. MUAT NAIK ARTWORK -->
                        <div
                            x-data="{
                                isUploading: false,
                                progress: 0,
                                imagePreview: null,
                                fileName: '',
                                isPdf: false,
                                handleFile(event) {
                                    const file = event.target.files[0];
                                    if (!file) return;

                                    this.fileName = file.name;
                                    this.isPdf = file.type === 'application/pdf';

                                    if (file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { this.imagePreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    } else {
                                        this.imagePreview = null;
                                    }
                                }
                            }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="border-2 border-dashed border-blue-300 p-5 rounded-xl bg-blue-50/50"
                        >
                            <label class="block text-xs font-bold text-gray-800 uppercase mb-2">Muat Naik Fail Artwork (Gambar / PDF)</label>

                            <input type="file" wire:model="artwork" @change="handleFile" accept="image/*,.pdf" class="text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">

                            <!-- BAR KEMAJUAN -->
                            <div x-show="isUploading" class="mt-3" style="display: none;">
                                <div class="flex justify-between text-xs font-bold text-blue-800 mb-1">
                                    <span>⏳ Memuat naik...</span>
                                    <span x-text="progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                                </div>
                            </div>

                            <!-- LIVE PREVIEW -->
                            <div x-show="imagePreview || isPdf" class="mt-4 p-4 bg-white rounded-xl border border-gray-200 flex items-center space-x-4 shadow-md" style="display: none;">
                                <template x-if="imagePreview">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative group cursor-pointer flex-shrink-0" @click="showZoomModal = true">
                                            <img :src="imagePreview" class="w-24 h-24 object-cover rounded-lg border-2 border-blue-500 shadow-sm">
                                            <div class="absolute inset-0 bg-blue-900/60 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition-opacity">🔍 Zoom</div>
                                        </div>
                                        <div>
                                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full">✓ Gambar Dikesan</span>
                                            <p class="text-sm font-bold text-gray-800 mt-1" x-text="fileName"></p>
                                            <button type="button" @click="showZoomModal = true" class="mt-2 text-xs text-blue-600 font-bold hover:underline">🔎 Klik Untuk Besarkan</button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="isPdf">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-lg flex items-center justify-center font-bold text-3xl flex-shrink-0">📄</div>
                                        <div>
                                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full">✓ PDF Dikesan</span>
                                            <p class="text-sm font-bold text-gray-800 mt-1" x-text="fileName"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- MODAL ZOOM -->
                            <div x-show="showZoomModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm" x-transition>
                                <div class="relative bg-white rounded-xl p-2 max-w-3xl max-h-[90vh] overflow-auto shadow-2xl" @click.away="showZoomModal = false">
                                    <button type="button" @click="showZoomModal = false" class="absolute top-3 right-3 bg-red-600 hover:bg-red-700 text-white rounded-full p-2 text-xs font-bold shadow-md">✕ Tutup</button>
                                    <img :src="imagePreview" class="w-full h-auto rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- 4. REMARK -->
                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Catatan Tambahan</label>
                            <textarea wire:model="remarks" rows="2" placeholder="Contoh: Sila tebuk lubang" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm"></textarea>
                        </div>

                        <!-- 5. PENGHANTARAN -->
                        <div class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <label class="block text-xs font-bold text-gray-800 uppercase">Kaedah Penghantaran *</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="fulfillment_group" wire:model.live="fulfillment_type" value="pickup" class="text-blue-600 border-gray-300 focus:ring-blue-500 w-4 h-4">
                                    <span class="ml-2 text-sm font-bold text-gray-800">🏬 Ambil Sendiri</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="fulfillment_group" wire:model.live="fulfillment_type" value="courier" class="text-blue-600 border-gray-300 focus:ring-blue-500 w-4 h-4">
                                    <span class="ml-2 text-sm font-bold text-gray-800">🚚 Kurier (+RM {{ number_format($courierFee, 2) }})</span>
                                </label>
                            </div>

                            @if ($fulfillment_type === 'courier')
                                <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200 space-y-3">

                                    @if (count($savedAddresses) > 0)
                                        <div>
                                            <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Pilih Alamat Tersimpan</label>
                                            <select wire:model.live="selected_address" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 shadow-sm">
                                                @foreach ($savedAddresses as $key => $addr)
                                                    <option value="{{ $key }}">{{ $addr['label'] }}</option>
                                                @endforeach
                                                <option value="manual">Taip alamat lain...</option>
                                            </select>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-xs font-bold text-gray-800 uppercase mb-1">Alamat Penghantaran *</label>
                                        <textarea wire:model="shipping_address" rows="3" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 shadow-sm"></textarea>
                                        @error('shipping_address') <span class="text-red-600 text-xs font-bold mt-1 block">⚠️ {{ $message }}</span> @enderror
                                        @if (count($savedAddresses) === 0)
                                            <p class="text-xs text-gray-500 mt-1">Tip: simpan alamat di <a href="{{ route('profile.edit') }}" class="text-blue-600 font-semibold">Profil</a> supaya tak perlu taip semula.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- 6. BUTANG SAHKAN -->
                        <div class="flex items-center justify-between p-5 bg-slate-900 rounded-xl shadow-xl text-white">
                            <div>
                                <span class="text-xs text-slate-300 uppercase font-bold block">Jumlah Anggaran Harga:</span>
                                <span class="text-3xl font-extrabold text-amber-400">RM {{ number_format($total_price, 2) }}</span>
                            </div>
                            <button type="submit" style="background-color: #1d4ed8 !important; color: #ffffff !important; opacity: 1 !important;" class="hover:bg-blue-700 active:bg-blue-800 text-white font-black px-8 py-3.5 rounded-lg text-sm shadow-lg transition-all duration-150 flex items-center cursor-pointer border-2 border-blue-400">
                                Sahkan & Teruskan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>