<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan Sistem
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 shadow">
                <svg class="flex-shrink-0 w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 shadow">
                <svg class="flex-shrink-0 w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b">⚙️ Pengaturan Logo & Judul</h3>

                {{-- ═══════════════════════════════════════════════════════
                     PENTING: form hapus logo HARUS di LUAR form utama
                     karena HTML tidak memperbolehkan nested <form>
                ═══════════════════════════════════════════════════════ --}}

                @php
                    $logoPreviewUrl = null;
                    if ($settings['app_logo']) {
                        if (file_exists(public_path($settings['app_logo']))) {
                            $logoPreviewUrl = asset($settings['app_logo']);
                        } elseif (file_exists(storage_path('app/public/' . $settings['app_logo']))) {
                            $logoPreviewUrl = asset('storage/' . $settings['app_logo']);
                        }
                    }
                @endphp

                {{-- Logo preview + hapus (DI LUAR form utama) --}}
                @if($logoPreviewUrl)
                <div class="mb-6 flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <img src="{{ $logoPreviewUrl }}"
                         alt="Logo" class="w-20 h-20 object-contain rounded-lg border border-gray-200 bg-white shadow-sm p-1">
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Logo Terpasang</p>
                        <p class="text-xs text-gray-500 mb-3">Ganti dengan memilih file baru di form bawah, atau hapus logo.</p>
                        {{-- Form hapus: STANDALONE, tidak di dalam form lain --}}
                        <form id="form-delete-logo"
                              action="{{ route('admin.settings.deleteLogo') }}"
                              method="POST">
                            @csrf
                            <button type="button"
                                    class="text-xs font-semibold text-red-600 hover:text-red-800 transition flex items-center gap-1"
                                    onclick="if(confirm('Hapus logo ini?')) document.getElementById('form-delete-logo').submit()">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Logo
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- ── Form utama simpan pengaturan ── --}}
                <form action="{{ route('admin.settings.update') }}" method="POST"
                      enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- App Title -->
                    <div>
                        <label for="app_title" class="block mb-2 text-sm font-semibold text-gray-700">Judul Utama Aplikasi</label>
                        <input type="text" name="app_title" id="app_title"
                               value="{{ old('app_title', $settings['app_title']) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-3"
                               placeholder="Contoh: Yudisium Santri" required>
                        @error('app_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- App Subtitle -->
                    <div>
                        <label for="app_subtitle" class="block mb-2 text-sm font-semibold text-gray-700">Sub-Judul / Keterangan</label>
                        <input type="text" name="app_subtitle" id="app_subtitle"
                               value="{{ old('app_subtitle', $settings['app_subtitle']) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-3"
                               placeholder="Contoh: Angkatan ke-32 Tahun 2026">
                        @error('app_subtitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">
                            {{ $logoPreviewUrl ? 'Ganti Logo' : 'Upload Logo Aplikasi' }}
                        </label>

                        <div class="flex items-center justify-center w-full" x-data="{ preview: null }">
                            <label for="app_logo"
                                   class="flex flex-col items-center justify-center w-full h-36 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <template x-if="preview">
                                    <img :src="preview" class="h-28 object-contain rounded" alt="preview">
                                </template>
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V8"/></svg>
                                        <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> logo</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, WebP (Max. 2MB)</p>
                                    </div>
                                </template>
                                <input id="app_logo" name="app_logo" type="file" class="hidden" accept="image/*"
                                       @change="preview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-lg text-sm px-5 py-3 transition">
                            💾 Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Zona Berbahaya (Danger Zone) ── --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-8 mt-8 border border-red-100">
                <h3 class="text-lg font-semibold text-red-600 mb-6 pb-3 border-b border-red-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Zona Berbahaya (Danger Zone)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Reset Nilai -->
                    <div class="p-6 bg-red-50 rounded-xl border border-red-100 flex flex-col justify-between">
                        <div>
                            <h4 class="text-md font-bold text-red-800 mb-2">Reset Data Nilai</h4>
                            <p class="text-sm text-red-600 mb-4">Fitur ini akan menghapus <strong>seluruh data nilai</strong> santri secara permanen. Data siswa dan kelas akan tetap utuh.</p>
                        </div>
                        <form id="form-reset-scores" action="{{ route('admin.settings.resetScores') }}" method="POST">
                            @csrf
                            <button type="button" 
                                    class="w-full text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition"
                                    onclick="if(confirm('Peringatan: Anda yakin ingin menghapus SEMUA DATA NILAI? Aksi ini tidak dapat dibatalkan.')) document.getElementById('form-reset-scores').submit();">
                                🗑️ Reset Semua Nilai
                            </button>
                        </form>
                    </div>

                    <!-- Reset Siswa -->
                    <div class="p-6 bg-red-50 rounded-xl border border-red-100 flex flex-col justify-between">
                        <div>
                            <h4 class="text-md font-bold text-red-800 mb-2">Reset Data Siswa</h4>
                            <p class="text-sm text-red-600 mb-4">Fitur ini akan menghapus <strong>seluruh data siswa beserta nilainya</strong>. Gunakan saat pergantian tahun ajaran baru.</p>
                        </div>
                        <form id="form-reset-students" action="{{ route('admin.settings.resetStudents') }}" method="POST">
                            @csrf
                            <button type="button" 
                                    class="w-full text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition"
                                    onclick="if(confirm('Peringatan: Anda yakin ingin menghapus SEMUA DATA SISWA beserta NILAINYA? Aksi ini tidak dapat dibatalkan.')) document.getElementById('form-reset-students').submit();">
                                ⚠️ Reset Semua Siswa & Nilai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
