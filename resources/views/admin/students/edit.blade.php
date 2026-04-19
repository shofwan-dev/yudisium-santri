<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.students.index') }}"
               class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800">Edit Data Santri</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-8">

                @if($errors->any())
                <div class="p-4 mb-5 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                    <p class="font-semibold mb-1">⚠️ Terdapat kesalahan input:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data"
                      class="space-y-5" x-data="{ preview: null }">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="no" class="block mb-1.5 text-sm font-semibold text-gray-700">No. Induk</label>
                            <input type="text" name="no" id="no" value="{{ old('no', $student->no) }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                                   placeholder="Opsional">
                        </div>
                        <div>
                            <label for="kelas_id" class="block mb-1.5 text-sm font-semibold text-gray-700">Kelas <span class="text-red-500">*</span></label>
                            <select name="kelas_id" id="kelas_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                                <option value="">-- Pilih --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('kelas_id', $student->kelas_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="nama" class="block mb-1.5 text-sm font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $student->nama) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                               placeholder="Masukkan nama lengkap santri" required>
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Foto Santri</label>
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 rounded-xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center shadow-sm">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-cover" alt="preview">
                                </template>
                                <template x-if="!preview">
                                    @php
                                        $fotoUrl = null;
                                        if ($student->foto) {
                                            if (file_exists(public_path($student->foto))) {
                                                $fotoUrl = asset($student->foto);
                                            } elseif (file_exists(storage_path('app/public/' . $student->foto))) {
                                                $fotoUrl = asset('storage/' . $student->foto);
                                            }
                                        }
                                    @endphp
                                    @if($fotoUrl)
                                        <img src="{{ $fotoUrl }}" class="w-full h-full object-cover" alt="{{ $student->nama }}">
                                    @else
                                        <div class="text-2xl font-bold text-green-600">{{ strtoupper(substr($student->nama, 0, 1)) }}</div>
                                    @endif
                                </template>
                            </div>
                            <div class="flex-1">
                                <label for="foto" class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V8"/></svg>
                                        <p class="text-xs text-gray-500">{{ $student->foto ? 'Ganti foto' : 'Upload foto' }}</p>
                                        <p class="text-xs text-gray-400">JPG / PNG, max 2MB</p>
                                    </div>
                                    <input id="foto" name="foto" type="file" class="hidden" accept="image/*"
                                           @change="preview = URL.createObjectURL($event.target.files[0])">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti foto.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-lg text-sm px-5 py-3 transition text-center">
                            💾 Update Data
                        </button>
                        <a href="{{ route('admin.students.index') }}"
                           class="flex-1 text-center text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-semibold rounded-lg text-sm px-5 py-3 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
