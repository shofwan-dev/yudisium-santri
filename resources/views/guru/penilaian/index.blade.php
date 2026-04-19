<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Input Penilaian Santri
            </h2>
            <span class="text-sm text-gray-500">Guru: <strong>{{ auth()->user()->name }}</strong></span>
        </div>
    </x-slot>

    <div class="py-6"
         x-data="penilaianApp()"
         x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── Notifikasi ── --}}
            @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-5 text-green-800 rounded-xl bg-green-50 border border-green-200 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            {{-- ── Filter Kelas ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Filter Kelas</label>
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('guru.penilaian.index') }}"
                           class="px-4 py-2 rounded-lg text-sm font-semibold transition
                                  {{ $selectedClass == '' ? 'bg-navy-700 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                           style="{{ $selectedClass == '' ? 'background:#1e3a5f;color:#fff;' : '' }}">
                            🏫 Semua Kelas
                        </a>
                        @foreach($classes as $c)
                        <a href="{{ route('guru.penilaian.index', ['kelas_id' => $c->id]) }}"
                           class="px-4 py-2 rounded-lg text-sm font-semibold transition"
                           style="{{ $selectedClass == $c->id ? 'background:#1e3a5f;color:#fff;' : 'background:#f3f4f6;color:#4b5563;' }}">
                            {{ $c->nama_kelas }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-3">
                    {{-- Auto-save indicator --}}
                    <div x-show="saving" class="flex items-center gap-2 text-sm text-blue-600">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </div>
                    <div x-show="!saving && lastSaved" x-cloak class="flex items-center gap-1.5 text-sm text-green-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="'Tersimpan ' + lastSaved"></span>
                    </div>
                </div>
            </div>

            {{-- ── Tabel Penilaian ── --}}
            @if(count($students) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            {{-- ── Row 1: main headers — NAVY ── --}}
                            <tr style="background:#1e3a5f;" class="text-white text-sm">
                                <th class="px-4 py-3.5 text-left font-semibold w-10">NO</th>
                                <th class="px-4 py-3.5 text-left font-semibold">NAMA SISWA</th>
                                <th class="px-4 py-3.5 text-center font-semibold">FOTO</th>
                                <th class="px-4 py-3.5 text-center font-semibold">KELAS</th>
                                {{-- Akhlaq --}}
                                <th class="px-2 py-3.5 text-center font-semibold border-l-2 border-white/20" colspan="2"
                                    style="background:rgba(255,255,255,0.10)">
                                    AKHLAQ
                                </th>
                                {{-- Disiplin --}}
                                <th class="px-2 py-3.5 text-center font-semibold border-l-2 border-white/20" colspan="2"
                                    style="background:rgba(255,255,255,0.06)">
                                    DISIPLIN
                                </th>
                                {{-- Tanggung Jawab --}}
                                <th class="px-2 py-3.5 text-center font-semibold border-l-2 border-white/20" colspan="2"
                                    style="background:rgba(255,255,255,0.10)">
                                    TANGGUNG JAWAB
                                </th>
                                <th class="px-3 py-3.5 text-center font-semibold w-24">AKSI</th>
                            </tr>
                            {{-- ── Row 2: sub-headers ── --}}
                            <tr style="background:#16304f;" class="text-blue-100 text-xs font-medium">
                                <th colspan="4"></th>
                                {{-- Akhlaq sub --}}
                                <th class="px-2 py-2 text-center border-l-2 border-white/20">Nilai</th>
                                <th class="px-2 py-2 text-center">Predikat</th>
                                {{-- Disiplin sub --}}
                                <th class="px-2 py-2 text-center border-l-2 border-white/20">Nilai</th>
                                <th class="px-2 py-2 text-center">Predikat</th>
                                {{-- TJ sub --}}
                                <th class="px-2 py-2 text-center border-l-2 border-white/20">Nilai</th>
                                <th class="px-2 py-2 text-center">Predikat</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $i => $student)
                            @php $score = $student->scores->first(); @endphp
                            <tr class="hover:bg-gray-50/80 transition"
                                x-data="{
                                    studentId:  {{ $student->id }},
                                    akhlak:     {{ $score?->akhlak_nilai ?? 'null' }},
                                    disiplin:   {{ $score?->disiplin_nilai ?? 'null' }},
                                    tj:         {{ $score?->tanggung_jawab_nilai ?? 'null' }},
                                    saved:      {{ $score ? 'true' : 'false' }},
                                    saving:     false,
                                    timer:      null,

                                    predikat(n) {
                                        if (n === null || n === '' || isNaN(n)) return '-';
                                        let v = parseInt(n);
                                        if (v >= 84) return 'TB';
                                        if (v >= 76) return 'BS';
                                        if (v >= 68) return 'B';
                                        if (v >= 60) return 'C';
                                        return 'K';
                                    },
                                    badgeClass(n) {
                                        if (n === null || n === '' || isNaN(n)) return 'bg-gray-100 text-gray-400';
                                        let v = parseInt(n);
                                        if (v >= 84) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
                                        if (v >= 76) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
                                        if (v >= 68) return 'bg-cyan-100 text-cyan-700 ring-1 ring-cyan-300';
                                        if (v >= 60) return 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-300';
                                        return 'bg-red-100 text-red-700 ring-1 ring-red-300';
                                    },
                                    scheduleAutoSave() {
                                        clearTimeout(this.timer);
                                        this.saved = false;
                                        this.timer = setTimeout(() => this.doSave(), 800);
                                    },
                                    doSave() {
                                        this.saving = true;
                                        $store.app.saving = true;
                                        fetch('{{ route('guru.penilaian.store') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                student_id:           this.studentId,
                                                akhlak_nilai:         this.akhlak,
                                                disiplin_nilai:       this.disiplin,
                                                tanggung_jawab_nilai: this.tj
                                            })
                                        })
                                        .then(r => r.json())
                                        .then(data => {
                                            this.saving = false;
                                            if (data.success) {
                                                this.saved = true;
                                                $store.app.saving = false;
                                                let now = new Date();
                                                $store.app.lastSaved = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
                                            }
                                        })
                                        .catch(() => { this.saving = false; $store.app.saving = false; });
                                    }
                                }">

                                {{-- NO --}}
                                <td class="px-4 py-3 text-gray-400 text-xs font-medium">
                                    {{ $student->no ?? ($i + 1) }}
                                </td>

                                {{-- NAMA SISWA --}}
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $student->nama }}</p>
                                </td>

                                {{-- FOTO --}}
                                <td class="px-4 py-2 text-center">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-gray-200 bg-green-100 mx-auto flex items-center justify-center shadow-sm">
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
                                            <span class="font-bold text-green-700 text-xl">{{ strtoupper(substr($student->nama, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- KELAS --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full whitespace-nowrap">
                                        {{ $student->classRoom->nama_kelas }}
                                    </span>
                                </td>

                                {{-- Akhlak: Nilai --}}
                                <td class="px-2 py-3 border-l border-gray-100">
                                    <input type="number" min="0" max="100"
                                           x-model="akhlak"
                                           @input="scheduleAutoSave()"
                                           class="w-16 text-center text-sm font-semibold border rounded-lg py-1.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition
                                                  bg-gray-50 border-gray-200 text-gray-800"
                                           placeholder="0-100">
                                </td>
                                {{-- Akhlak: Predikat --}}
                                <td class="px-2 py-3">
                                    <span class="inline-flex items-center justify-center w-12 h-7 rounded-full text-xs font-bold transition"
                                          :class="badgeClass(akhlak)"
                                          x-text="predikat(akhlak)">-</span>
                                </td>

                                {{-- Disiplin: Nilai --}}
                                <td class="px-2 py-3 border-l border-gray-100">
                                    <input type="number" min="0" max="100"
                                           x-model="disiplin"
                                           @input="scheduleAutoSave()"
                                           class="w-16 text-center text-sm font-semibold border rounded-lg py-1.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition
                                                  bg-gray-50 border-gray-200 text-gray-800"
                                           placeholder="0-100">
                                </td>
                                {{-- Disiplin: Predikat --}}
                                <td class="px-2 py-3">
                                    <span class="inline-flex items-center justify-center w-12 h-7 rounded-full text-xs font-bold transition"
                                          :class="badgeClass(disiplin)"
                                          x-text="predikat(disiplin)">-</span>
                                </td>

                                {{-- TJ: Nilai --}}
                                <td class="px-2 py-3 border-l border-gray-100">
                                    <input type="number" min="0" max="100"
                                           x-model="tj"
                                           @input="scheduleAutoSave()"
                                           class="w-16 text-center text-sm font-semibold border rounded-lg py-1.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition
                                                  bg-gray-50 border-gray-200 text-gray-800"
                                           placeholder="0-100">
                                </td>
                                {{-- TJ: Predikat --}}
                                <td class="px-2 py-3">
                                    <span class="inline-flex items-center justify-center w-12 h-7 rounded-full text-xs font-bold transition"
                                          :class="badgeClass(tj)"
                                          x-text="predikat(tj)">-</span>
                                </td>

                                {{-- Aksi: Status + Edit --}}
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Save status icon --}}
                                        <template x-if="saving">
                                            <svg class="w-4 h-4 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!saving && saved">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                        <template x-if="!saving && !saved">
                                            <span class="inline-block w-2 h-2 rounded-full bg-gray-300"></span>
                                        </template>

                                        {{-- Edit button --}}
                                        <button type="button"
                                                @click="$dispatch('open-edit-modal', {
                                                    id: studentId,
                                                    nama: '{{ addslashes($student->nama) }}',
                                                    akhlak: akhlak,
                                                    disiplin: disiplin,
                                                    tj: tj
                                                })"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-lg transition"
                                                style="background:#e8f0fe;color:#1e3a5f;"
                                                onmouseover="this.style.background='#c7d7f8'"
                                                onmouseout="this.style.background='#e8f0fe'">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer: total & legend --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="font-medium text-gray-500">Predikat:</span>
                        <span class="bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300 px-2 py-0.5 rounded-full font-bold">TB ≥84</span>
                        <span class="bg-blue-100 text-blue-700 ring-1 ring-blue-300 px-2 py-0.5 rounded-full font-bold">BS ≥76</span>
                        <span class="bg-cyan-100 text-cyan-700 ring-1 ring-cyan-300 px-2 py-0.5 rounded-full font-bold">B ≥68</span>
                        <span class="bg-yellow-100 text-yellow-700 ring-1 ring-yellow-300 px-2 py-0.5 rounded-full font-bold">C ≥60</span>
                        <span class="bg-red-100 text-red-700 ring-1 ring-red-300 px-2 py-0.5 rounded-full font-bold">K &lt;60</span>
                    </div>
                    <p class="text-xs text-gray-400">
                        ✅ Nilai tersimpan otomatis saat Anda mengetik
                        · Total: <strong class="text-gray-600">{{ count($students) }} santri</strong>
                    </p>
                </div>
            </div>

            @else
            {{-- Empty state --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-700 mb-1">Tidak ada santri</h3>
                <p class="text-sm text-gray-400">Data santri belum tersedia atau belum ada yang terdaftar di kelas ini.</p>
            </div>
            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         EDIT MODAL
    ══════════════════════════════════════════════════════ --}}
    <div x-data="editModal()"
         x-on:open-edit-modal.window="open($event.detail)"
         x-show="isOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>

        {{-- Modal panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden"
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="px-6 py-4 flex items-center justify-between" style="background:#1e3a5f;">
                <div>
                    <h3 class="text-white font-bold text-base">✏️ Edit Nilai</h3>
                    <p class="text-blue-200 text-xs mt-0.5" x-text="'Santri: ' + form.nama"></p>
                </div>
                <button @click="close()" class="text-white/70 hover:text-white transition text-xl leading-none">&times;</button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Pesan sukses --}}
                <div x-show="successMsg" x-cloak
                     class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="successMsg"></span>
                </div>

                {{-- Akhlak --}}
                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Akhlak</label>
                        <input type="number" min="0" max="100" x-model="form.akhlak"
                               @input="calcPredikat()"
                               class="w-full text-center text-sm font-semibold border rounded-lg py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-gray-50 border-gray-200"
                               placeholder="0 - 100">
                    </div>
                    <div class="text-center pt-5">
                        <span class="inline-flex items-center justify-center w-16 h-8 rounded-full text-sm font-bold"
                              :class="badgeClass(form.akhlak)"
                              x-text="predikat(form.akhlak)">-</span>
                    </div>
                </div>

                {{-- Disiplin --}}
                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Disiplin</label>
                        <input type="number" min="0" max="100" x-model="form.disiplin"
                               @input="calcPredikat()"
                               class="w-full text-center text-sm font-semibold border rounded-lg py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-gray-50 border-gray-200"
                               placeholder="0 - 100">
                    </div>
                    <div class="text-center pt-5">
                        <span class="inline-flex items-center justify-center w-16 h-8 rounded-full text-sm font-bold"
                              :class="badgeClass(form.disiplin)"
                              x-text="predikat(form.disiplin)">-</span>
                    </div>
                </div>

                {{-- Tanggung Jawab --}}
                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Tanggung Jawab</label>
                        <input type="number" min="0" max="100" x-model="form.tj"
                               @input="calcPredikat()"
                               class="w-full text-center text-sm font-semibold border rounded-lg py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-gray-50 border-gray-200"
                               placeholder="0 - 100">
                    </div>
                    <div class="text-center pt-5">
                        <span class="inline-flex items-center justify-center w-16 h-8 rounded-full text-sm font-bold"
                              :class="badgeClass(form.tj)"
                              x-text="predikat(form.tj)">-</span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3 justify-end">
                <button @click="close()"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="save()"
                        :disabled="saving"
                        class="px-5 py-2 text-sm font-semibold text-white rounded-lg transition flex items-center gap-2"
                        style="background:#1e3a5f;"
                        onmouseover="if(!this.disabled)this.style.background='#163051'"
                        onmouseout="if(!this.disabled)this.style.background='#1e3a5f'">
                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="saving ? 'Menyimpan...' : '💾 Simpan Nilai'"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Alpine store for shared saving state
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                saving: false,
                lastSaved: null
            });
        });

        function penilaianApp() {
            return {
                saving:   false,
                lastSaved: null,
                init() {
                    this.$watch('$store.app.saving',   v => this.saving   = v);
                    this.$watch('$store.app.lastSaved', v => this.lastSaved = v);
                }
            }
        }

        function editModal() {
            return {
                isOpen: false,
                saving: false,
                successMsg: '',
                form: { id: null, nama: '', akhlak: null, disiplin: null, tj: null },

                open(detail) {
                    this.form = {
                        id:       detail.id,
                        nama:     detail.nama,
                        akhlak:   detail.akhlak,
                        disiplin: detail.disiplin,
                        tj:       detail.tj,
                    };
                    this.successMsg = '';
                    this.isOpen = true;
                },

                close() {
                    this.isOpen = false;
                },

                predikat(n) {
                    if (n === null || n === '' || isNaN(n)) return '-';
                    let v = parseInt(n);
                    if (v >= 84) return 'TB';
                    if (v >= 76) return 'BS';
                    if (v >= 68) return 'B';
                    if (v >= 60) return 'C';
                    return 'K';
                },

                badgeClass(n) {
                    if (n === null || n === '' || isNaN(n)) return 'bg-gray-100 text-gray-400';
                    let v = parseInt(n);
                    if (v >= 84) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
                    if (v >= 76) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
                    if (v >= 68) return 'bg-cyan-100 text-cyan-700 ring-1 ring-cyan-300';
                    if (v >= 60) return 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-300';
                    return 'bg-red-100 text-red-700 ring-1 ring-red-300';
                },

                calcPredikat() { /* live update via x-text binding */ },

                save() {
                    this.saving = true;
                    this.successMsg = '';
                    fetch('{{ route('guru.penilaian.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            student_id:           this.form.id,
                            akhlak_nilai:         this.form.akhlak,
                            disiplin_nilai:       this.form.disiplin,
                            tanggung_jawab_nilai: this.form.tj
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.saving = false;
                        if (data.success) {
                            this.successMsg = '✅ Nilai berhasil disimpan!';
                            // Reflect changes in the table row via event
                            window.dispatchEvent(new CustomEvent('nilai-updated', {
                                detail: {
                                    id:       this.form.id,
                                    akhlak:   this.form.akhlak,
                                    disiplin: this.form.disiplin,
                                    tj:       this.form.tj,
                                }
                            }));
                            setTimeout(() => this.close(), 1200);
                        }
                    })
                    .catch(() => { this.saving = false; });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
