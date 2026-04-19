<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.students.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            <span class="text-gray-300">/</span>
            <h2 class="font-bold text-xl text-gray-800">Detail Penilaian Santri</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ══ Student Info Card ══ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="flex items-center gap-6">
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
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-green-100 border-2 border-gray-200 flex items-center justify-center shadow-sm flex-shrink-0">
                        @if($fotoUrl)
                            <img src="{{ $fotoUrl }}" alt="{{ $student->nama }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-green-700">{{ strtoupper(substr($student->nama, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->nama }}</h1>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                            <span class="bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full font-semibold text-xs">
                                {{ $student->classRoom->nama_kelas }}
                            </span>
                            <span class="text-gray-400">No. {{ $student->no ?? '-' }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Dinilai oleh <strong class="text-gray-600">{{ $student->scores->count() }}</strong> guru
                        </p>
                    </div>
                </div>

                @if($student->scores->isNotEmpty())
                @php
                    $scores      = $student->scores;
                    $avgAkhlak   = round($scores->avg('akhlak_nilai'));
                    $avgDisiplin = round($scores->avg('disiplin_nilai'));
                    $avgTj       = round($scores->avg('tanggung_jawab_nilai'));
                @endphp
                <div class="sm:ml-auto flex flex-col sm:flex-row items-center gap-6 sm:gap-8 bg-gray-50/50 sm:bg-transparent rounded-xl p-4 sm:p-0 mt-4 sm:mt-0">
                    <div class="flex gap-4">
                        @foreach([
                            ['label' => 'Akhlak', 'val' => $avgAkhlak,   'color' => 'amber'],
                            ['label' => 'Disiplin','val' => $avgDisiplin, 'color' => 'blue'],
                            ['label' => 'T. Jawab','val' => $avgTj,       'color' => 'purple'],
                        ] as $avg)
                        <div class="text-center">
                            <div class="text-3xl font-black" style="color:#1e3a5f;">{{ $avg['val'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 font-medium">{{ $avg['label'] }}</div>
                            <div class="text-xs font-bold mt-0.5
                                {{ \App\Models\Score::getPredikat($avg['val']) === 'TB' ? 'text-green-600' :
                                  (\App\Models\Score::getPredikat($avg['val']) === 'BS' ? 'text-blue-600' :
                                  (\App\Models\Score::getPredikat($avg['val']) === 'B'  ? 'text-sky-500' :
                                  (\App\Models\Score::getPredikat($avg['val']) === 'C'  ? 'text-yellow-500' : 'text-red-500'))) }}">
                                {{ \App\Models\Score::getPredikat($avg['val']) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="w-full sm:w-auto border-t sm:border-t-0 sm:border-l border-gray-200 pt-4 sm:pt-0 sm:pl-8 flex sm:block justify-center">
                        <a href="{{ route('admin.students.export_single', $student->id) }}"
                           title="Export Excel"
                           class="inline-flex items-center gap-2 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-semibold rounded-lg text-sm px-5 py-2.5 transition shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </a>
                    </div>
                </div>
                @endif
            </div>

            {{-- ══ Per-Guru Scores ══ --}}
            @if($student->scores->isNotEmpty())

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2" style="background:#1e3a5f;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="font-bold text-white">Nilai dari Setiap Guru</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:#16304f;" class="text-blue-100 text-xs font-semibold uppercase">
                                <th class="px-5 py-3 text-left">Guru Penilai</th>
                                <th class="px-4 py-3 text-center" colspan="2" style="background:rgba(255,255,255,0.05)">Akhlak</th>
                                <th class="px-4 py-3 text-center" colspan="2">Disiplin</th>
                                <th class="px-4 py-3 text-center" colspan="2" style="background:rgba(255,255,255,0.05)">Tanggung Jawab</th>
                                <th class="px-4 py-3 text-center">Tanggal</th>
                            </tr>
                            <tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
                                <th class="px-5 py-2"></th>
                                <th class="px-3 py-2 text-center bg-amber-50">Nilai</th>
                                <th class="px-3 py-2 text-center bg-amber-50">Predikat</th>
                                <th class="px-3 py-2 text-center bg-blue-50">Nilai</th>
                                <th class="px-3 py-2 text-center bg-blue-50">Predikat</th>
                                <th class="px-3 py-2 text-center bg-purple-50">Nilai</th>
                                <th class="px-3 py-2 text-center bg-purple-50">Predikat</th>
                                <th class="px-4 py-2 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($student->scores as $score)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                             style="background:#1e3a5f;">
                                            {{ strtoupper(substr($score->guru->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $score->guru->name ?? 'Guru #' . $score->guru_id }}</p>
                                            <p class="text-xs text-gray-400">{{ $score->guru->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Akhlak --}}
                                <td class="px-3 py-3 text-center bg-amber-50 font-bold text-gray-800">
                                    {{ $score->akhlak_nilai ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-amber-50">
                                    @if($score->akhlak_predikat)
                                        @php $p = $score->akhlak_predikat; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                            {{ $p === 'TB' ? 'bg-green-600 text-white' : ($p === 'BS' ? 'bg-blue-500 text-white' : ($p === 'B' ? 'bg-sky-400 text-white' : ($p === 'C' ? 'bg-yellow-400 text-white' : 'bg-red-500 text-white'))) }}">
                                            {{ $p }}
                                        </span>
                                    @else <span class="text-gray-400">-</span> @endif
                                </td>

                                {{-- Disiplin --}}
                                <td class="px-3 py-3 text-center bg-blue-50 font-bold text-gray-800">
                                    {{ $score->disiplin_nilai ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-blue-50">
                                    @if($score->disiplin_predikat)
                                        @php $p = $score->disiplin_predikat; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                            {{ $p === 'TB' ? 'bg-green-600 text-white' : ($p === 'BS' ? 'bg-blue-500 text-white' : ($p === 'B' ? 'bg-sky-400 text-white' : ($p === 'C' ? 'bg-yellow-400 text-white' : 'bg-red-500 text-white'))) }}">
                                            {{ $p }}
                                        </span>
                                    @else <span class="text-gray-400">-</span> @endif
                                </td>

                                {{-- Tanggung Jawab --}}
                                <td class="px-3 py-3 text-center bg-purple-50 font-bold text-gray-800">
                                    {{ $score->tanggung_jawab_nilai ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center bg-purple-50">
                                    @if($score->tanggung_jawab_predikat)
                                        @php $p = $score->tanggung_jawab_predikat; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                            {{ $p === 'TB' ? 'bg-green-600 text-white' : ($p === 'BS' ? 'bg-blue-500 text-white' : ($p === 'B' ? 'bg-sky-400 text-white' : ($p === 'C' ? 'bg-yellow-400 text-white' : 'bg-red-500 text-white'))) }}">
                                            {{ $p }}
                                        </span>
                                    @else <span class="text-gray-400">-</span> @endif
                                </td>

                                <td class="px-4 py-3 text-center text-xs text-gray-400">
                                    {{ $score->updated_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        {{-- ══ Rata-rata footer row ══ --}}
                        <tfoot>
                            <tr class="border-t-2 border-green-200" style="background:#f0fdf4;">
                                <td class="px-5 py-3 font-bold text-green-700 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Rata-rata Final
                                </td>
                                @php
                                    $sc = $student->scores;
                                    $fAvg = [
                                        round($sc->avg('akhlak_nilai')),
                                        round($sc->avg('disiplin_nilai')),
                                        round($sc->avg('tanggung_jawab_nilai')),
                                    ];
                                    $fBg = ['bg-amber-100','bg-blue-100','bg-purple-100'];
                                @endphp
                                @foreach($fAvg as $idx => $val)
                                    @php $prd = \App\Models\Score::getPredikat($val); @endphp
                                    <td class="px-3 py-3 text-center {{ $fBg[$idx] }} font-black text-gray-900 text-base">{{ $val }}</td>
                                    <td class="px-3 py-3 text-center {{ $fBg[$idx] }}">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                                            {{ $prd === 'TB' ? 'bg-green-600 text-white' : ($prd === 'BS' ? 'bg-blue-500 text-white' : ($prd === 'B' ? 'bg-sky-400 text-white' : ($prd === 'C' ? 'bg-yellow-400 text-white' : 'bg-red-500 text-white'))) }}">
                                            {{ $prd }}
                                        </span>
                                    </td>
                                @endforeach
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-semibold text-gray-600">Santri ini belum mendapatkan penilaian dari guru manapun.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
