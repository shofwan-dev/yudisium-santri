<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Rekap Penilaian Santri
            </h2>
            <a href="{{ route('admin.students.create') }}"
               class="inline-flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-lg text-sm px-4 py-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Santri
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 shadow-sm" role="alert">
                <svg class="flex-shrink-0 w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <!-- Filter & Export Bar -->
            <div class="bg-white shadow-sm rounded-xl p-4 mb-4">
                <form id="filterForm" action="{{ route('admin.students.index') }}" method="GET"
                      class="flex flex-col sm:flex-row gap-3 items-end flex-wrap">
                    <div class="w-full sm:w-44">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Kelas</label>
                        <select name="kelas_id" id="filterKelas"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('kelas_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-60">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                               placeholder="Ketik nama santri...">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-1 text-white bg-gray-700 hover:bg-gray-900 font-semibold rounded-lg text-sm px-4 py-2.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                       class="inline-flex items-center gap-1 text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-semibold rounded-lg text-sm px-4 py-2.5 transition">
                        ✕ Reset
                    </a>

                    <!-- Export Button -->
                    <div class="sm:ml-auto">
                        <a href="{{ route('admin.students.export', ['kelas_id' => request('kelas_id')]) }}"
                           class="inline-flex items-center gap-2 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-semibold rounded-lg text-sm px-4 py-2.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>

            @php
            function predikatBadgeClass(?string $p): string {
                return match($p) {
                    'TB' => 'bg-green-600 text-white',
                    'BS' => 'bg-blue-500 text-white',
                    'B'  => 'bg-sky-400 text-white',
                    'C'  => 'bg-yellow-400 text-white',
                    'K'  => 'bg-red-500 text-white',
                    default => 'bg-gray-100 text-gray-400',
                };
            }
            @endphp

            <!-- Table: 1 baris per santri, nilai = rata-rata semua guru -->
            <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead>
                        <tr style="background:#1e3a5f;" class="text-white text-xs uppercase">
                            <th class="px-4 py-3 whitespace-nowrap">No</th>
                            <th class="px-4 py-3 whitespace-nowrap">Foto</th>
                            <th class="px-4 py-3 whitespace-nowrap">Nama Santri</th>
                            <th class="px-4 py-3 whitespace-nowrap">Kelas</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap" colspan="2"
                                style="background:rgba(255,255,255,0.10)">Akhlak (Rata²)</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap" colspan="2"
                                style="background:rgba(255,255,255,0.06)">Disiplin (Rata²)</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap" colspan="2"
                                style="background:rgba(255,255,255,0.10)">Tanggung Jawab (Rata²)</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Penilai</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                        <tr style="background:#16304f;" class="text-blue-100 text-xs">
                            <th colspan="4"></th>
                            <th class="px-3 py-2 text-center font-semibold">Nilai</th>
                            <th class="px-3 py-2 text-center font-semibold">Predikat</th>
                            <th class="px-3 py-2 text-center font-semibold">Nilai</th>
                            <th class="px-3 py-2 text-center font-semibold">Predikat</th>
                            <th class="px-3 py-2 text-center font-semibold">Nilai</th>
                            <th class="px-3 py-2 text-center font-semibold">Predikat</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $i => $s)
                            @php
                                $scores      = $s->scores;
                                $hasScores   = $scores->isNotEmpty();
                                $guruCount   = $scores->count();

                                $avgAkhlak   = $hasScores ? round($scores->avg('akhlak_nilai'))           : null;
                                $avgDisiplin = $hasScores ? round($scores->avg('disiplin_nilai'))          : null;
                                $avgTj       = $hasScores ? round($scores->avg('tanggung_jawab_nilai'))    : null;

                                $avgAkhlakP   = $avgAkhlak   !== null ? \App\Models\Score::getPredikat($avgAkhlak)   : null;
                                $avgDisiplinP = $avgDisiplin !== null ? \App\Models\Score::getPredikat($avgDisiplin) : null;
                                $avgTjP       = $avgTj       !== null ? \App\Models\Score::getPredikat($avgTj)       : null;

                                $fotoUrl = null;
                                if ($s->foto) {
                                    if (file_exists(public_path($s->foto))) {
                                        $fotoUrl = asset($s->foto);
                                    } elseif (file_exists(storage_path('app/public/' . $s->foto))) {
                                        $fotoUrl = asset('storage/' . $s->foto);
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $s->no ?? ($students->firstItem() + $i) }}</td>
                                <td class="px-4 py-3">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-green-100 border-2 border-gray-200 flex items-center justify-center shadow-sm">
                                        @if($fotoUrl)
                                            <img src="{{ $fotoUrl }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xl font-bold text-green-700">{{ strtoupper(substr($s->nama, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ $s->nama }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                                        {{ $s->classRoom->nama_kelas }}
                                    </span>
                                </td>

                                {{-- Akhlak rata-rata --}}
                                <td class="px-3 py-3 text-center bg-amber-50 font-bold text-gray-800">
                                    {{ $avgAkhlak ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-amber-50">
                                    @if($avgAkhlakP)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ predikatBadgeClass($avgAkhlakP) }}">{{ $avgAkhlakP }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Disiplin rata-rata --}}
                                <td class="px-3 py-3 text-center bg-blue-50 font-bold text-gray-800">
                                    {{ $avgDisiplin ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-blue-50">
                                    @if($avgDisiplinP)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ predikatBadgeClass($avgDisiplinP) }}">{{ $avgDisiplinP }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Tanggung Jawab rata-rata --}}
                                <td class="px-3 py-3 text-center bg-purple-50 font-bold text-gray-800">
                                    {{ $avgTj ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-purple-50">
                                    @if($avgTjP)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ predikatBadgeClass($avgTjP) }}">{{ $avgTjP }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Jumlah guru --}}
                                <td class="px-4 py-3 text-center">
                                    @if($hasScores)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            {{ $guruCount }} guru
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="inline-flex rounded-md shadow-sm" role="group">
                                        {{-- Detail --}}
                                        <a href="{{ route('admin.students.show', $s->id) }}"
                                           title="Detail Penilaian"
                                           class="inline-flex items-center p-2 text-sm font-medium text-blue-700 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:text-blue-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.students.edit', $s->id) }}"
                                           title="Edit Data Santri"
                                           class="inline-flex items-center p-2 text-sm font-medium text-yellow-600 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-yellow-700 focus:z-10 focus:ring-2 focus:ring-yellow-500 focus:text-yellow-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST"
                                              class="inline-flex"
                                              onsubmit="return confirm('Yakin hapus santri {{ addslashes($s->nama) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Santri"
                                                    class="inline-flex items-center p-2 text-sm font-medium text-red-600 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-500 focus:text-red-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-12 text-center text-gray-500">
                                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="font-medium">Belum ada data santri.</p>
                                    <p class="text-sm mt-1">Mulai tambahkan santri baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($students->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $students->links() }}
                </div>
                @endif
            </div>

            <!-- Legend -->
            <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-500">
                <span class="font-semibold text-gray-700">Predikat:</span>
                <span class="px-2 py-0.5 rounded-full bg-green-600 text-white font-bold">TB</span> Terbaik (≥84)
                <span class="px-2 py-0.5 rounded-full bg-blue-500 text-white font-bold">BS</span> Baik Sekali (≥76)
                <span class="px-2 py-0.5 rounded-full bg-sky-400 text-white font-bold">B</span> Baik (≥68)
                <span class="px-2 py-0.5 rounded-full bg-yellow-400 text-white font-bold">C</span> Cukup (≥60)
                <span class="px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">K</span> Kurang (&lt;60)
                <span class="ml-3 text-gray-400">· Nilai yang tampil adalah rata-rata dari semua guru penilai. Klik <strong>Detail</strong> untuk melihat penilaian per guru.</span>
            </div>
        </div>
    </div>
</x-app-layout>
