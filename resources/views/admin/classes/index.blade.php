<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Manajemen Kelas
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 shadow-sm" role="alert">
                <svg class="flex-shrink-0 w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- Add Class Form -->
            <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">➕ Tambah Kelas Baru</h3>
                <form action="{{ route('admin.classes.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                    @csrf
                    <div class="w-full sm:flex-1">
                        <label for="nama_kelas" class="block mb-1.5 text-sm font-medium text-gray-700">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="nama_kelas"
                               value="{{ old('nama_kelas') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                               placeholder="Contoh: XII IPA 1" required>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-semibold rounded-lg text-sm px-5 py-2.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Simpan
                    </button>
                </form>
            </div>

            <!-- Classes Table -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Kelas</th>
                            <th class="px-6 py-3">Jumlah Santri</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($classes as $i => $c)
                        <tr class="hover:bg-gray-50 transition" x-data="{ editing: false, nama: '{{ addslashes($c->nama_kelas) }}' }">
                            <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>

                            <!-- Inline Edit -->
                            <td class="px-6 py-3 font-semibold text-gray-900">
                                <span x-show="!editing">{{ $c->nama_kelas }}</span>
                                <form x-show="editing" action="{{ route('admin.classes.update', $c->id) }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nama_kelas" x-model="nama"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 p-2" required>
                                    <button type="submit" class="text-xs font-semibold text-white bg-green-600 hover:bg-green-700 px-3 py-2 rounded-lg transition">Simpan</button>
                                    <button type="button" @click="editing = false" class="text-xs font-semibold text-gray-600 hover:text-gray-800 px-2 py-2">Batal</button>
                                </form>
                            </td>

                            <td class="px-6 py-3">
                                <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full">
                                {{ $c->students_count }} santri
                                </span>
                            </td>

                            <td class="px-6 py-3 text-center" x-show="!editing">
                                <button type="button" @click="editing = true"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition mr-1">
                                    ✏️ Edit
                                </button>
                                <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Hapus kelas {{ addslashes($c->nama_kelas) }}? Semua data santri di kelas ini akan terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition {{ $c->students_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $c->students_count > 0 ? 'disabled title=Kelas masih memiliki santri' : '' }}>
                                        🗑 Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                <p class="font-medium">Belum ada data kelas.</p>
                                <p class="text-sm mt-1">Silakan tambahkan kelas di atas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
