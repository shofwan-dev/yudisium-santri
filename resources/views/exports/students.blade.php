<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Santri</th>
            <th>Kelas</th>
            <th>Penilai (Guru)</th>
            <th>Akhlak - Nilai</th>
            <th>Akhlak - Predikat</th>
            <th>Disiplin - Nilai</th>
            <th>Disiplin - Predikat</th>
            <th>Tanggung Jawab - Nilai</th>
            <th>Tanggung Jawab - Predikat</th>
            <th>Ibadah - Nilai</th>
            <th>Ibadah - Predikat</th>
            <th>Kepemimpinan - Nilai</th>
            <th>Kepemimpinan - Predikat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $i => $s)
            @php
                $scores   = $s->scores;
                $hasScores = $scores->isNotEmpty();
            @endphp

            @if($hasScores)
                {{-- Individual guru rows --}}
                @foreach($scores as $score)
                <tr>
                    <td>{{ $s->no ?? ($i + 1) }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->classRoom->nama_kelas }}</td>
                    <td>{{ $score->guru->name ?? 'Guru #' . $score->guru_id }}</td>
                    <td>{{ $score->akhlak_nilai ?? '-' }}</td>
                    <td>{{ $score->akhlak_predikat ?? '-' }}</td>
                    <td>{{ $score->disiplin_nilai ?? '-' }}</td>
                    <td>{{ $score->disiplin_predikat ?? '-' }}</td>
                    <td>{{ $score->tanggung_jawab_nilai ?? '-' }}</td>
                    <td>{{ $score->tanggung_jawab_predikat ?? '-' }}</td>
                    <td>{{ $score->ibadah_nilai ?? '-' }}</td>
                    <td>{{ $score->ibadah_predikat ?? '-' }}</td>
                    <td>{{ $score->kepemimpinan_nilai ?? '-' }}</td>
                    <td>{{ $score->kepemimpinan_predikat ?? '-' }}</td>
                </tr>
                @endforeach

                {{-- Rata-rata row --}}
                @php
                    $avgAkhlak   = round($scores->avg('akhlak_nilai'));
                    $avgDisiplin = round($scores->avg('disiplin_nilai'));
                    $avgTj       = round($scores->avg('tanggung_jawab_nilai'));
                    $avgIbadah   = round($scores->avg('ibadah_nilai'));
                    $avgKpm      = round($scores->avg('kepemimpinan_nilai'));
                @endphp
                <tr>
                    <td>{{ $s->no ?? ($i + 1) }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->classRoom->nama_kelas }}</td>
                    <td>RATA-RATA ({{ $scores->count() }} guru)</td>
                    <td>{{ $avgAkhlak }}</td>
                    <td>{{ \App\Models\Score::getPredikat($avgAkhlak) }}</td>
                    <td>{{ $avgDisiplin }}</td>
                    <td>{{ \App\Models\Score::getPredikat($avgDisiplin) }}</td>
                    <td>{{ $avgTj }}</td>
                    <td>{{ \App\Models\Score::getPredikat($avgTj) }}</td>
                    <td>{{ $avgIbadah }}</td>
                    <td>{{ \App\Models\Score::getPredikat($avgIbadah) }}</td>
                    <td>{{ $avgKpm }}</td>
                    <td>{{ \App\Models\Score::getPredikat($avgKpm) }}</td>
                </tr>
            @else
                <tr>
                    <td>{{ $s->no ?? ($i + 1) }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->classRoom->nama_kelas }}</td>
                    <td>Belum dinilai</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
