<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * Tampilkan semua santri (opsional filter kelas),
     * sudah termasuk nilai yang pernah diisi guru ini.
     */
    public function index(Request $request)
    {
        $classes = ClassRoom::orderBy('nama_kelas')->get();

        $query = Student::with([
                'classRoom',
                'scores' => fn($q) => $q->where('guru_id', auth()->id())
            ])
            ->orderBy('kelas_id')
            ->orderBy('no');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $students      = $query->get();
        $selectedClass = $request->kelas_id ?? '';

        return view('guru.penilaian.index', compact('classes', 'students', 'selectedClass'));
    }

    /**
     * Simpan / update satu baris nilai (AJAX per-row).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'           => 'required|exists:students,id',
            'akhlak_nilai'         => 'nullable|numeric|min:0|max:100',
            'disiplin_nilai'       => 'nullable|numeric|min:0|max:100',
            'tanggung_jawab_nilai' => 'nullable|numeric|min:0|max:100',
            'ibadah_nilai'         => 'nullable|numeric|min:0|max:100',
            'kepemimpinan_nilai'   => 'nullable|numeric|min:0|max:100',
        ]);

        $score = Score::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'guru_id'    => auth()->id(),
            ],
            [
                'akhlak_nilai'             => $validated['akhlak_nilai'],
                'akhlak_predikat'          => Score::getPredikat($validated['akhlak_nilai']),
                'disiplin_nilai'           => $validated['disiplin_nilai'],
                'disiplin_predikat'        => Score::getPredikat($validated['disiplin_nilai']),
                'tanggung_jawab_nilai'     => $validated['tanggung_jawab_nilai'],
                'tanggung_jawab_predikat'  => Score::getPredikat($validated['tanggung_jawab_nilai']),
                'ibadah_nilai'             => $validated['ibadah_nilai'],
                'ibadah_predikat'          => Score::getPredikat($validated['ibadah_nilai']),
                'kepemimpinan_nilai'       => $validated['kepemimpinan_nilai'],
                'kepemimpinan_predikat'    => Score::getPredikat($validated['kepemimpinan_nilai']),
            ]
        );

        return response()->json([
            'success'           => true,
            'akhlak_predikat'   => $score->akhlak_predikat,
            'disiplin_predikat' => $score->disiplin_predikat,
            'tj_predikat'       => $score->tanggung_jawab_predikat,
            'ibadah_predikat'   => $score->ibadah_predikat,
            'kpm_predikat'      => $score->kepemimpinan_predikat,
        ]);
    }

    /**
     * Simpan semua nilai sekaligus (POST form tradisional).
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'scores'                           => 'required|array',
            'scores.*.student_id'              => 'required|exists:students,id',
            'scores.*.akhlak_nilai'            => 'nullable|numeric|min:0|max:100',
            'scores.*.disiplin_nilai'          => 'nullable|numeric|min:0|max:100',
            'scores.*.tanggung_jawab_nilai'    => 'nullable|numeric|min:0|max:100',
            'scores.*.ibadah_nilai'            => 'nullable|numeric|min:0|max:100',
            'scores.*.kepemimpinan_nilai'      => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $row) {
            Score::updateOrCreate(
                [
                    'student_id' => $row['student_id'],
                    'guru_id'    => auth()->id(),
                ],
                [
                    'akhlak_nilai'            => $row['akhlak_nilai'] ?? null,
                    'akhlak_predikat'         => Score::getPredikat($row['akhlak_nilai'] ?? null),
                    'disiplin_nilai'          => $row['disiplin_nilai'] ?? null,
                    'disiplin_predikat'       => Score::getPredikat($row['disiplin_nilai'] ?? null),
                    'tanggung_jawab_nilai'    => $row['tanggung_jawab_nilai'] ?? null,
                    'tanggung_jawab_predikat' => Score::getPredikat($row['tanggung_jawab_nilai'] ?? null),
                    'ibadah_nilai'            => $row['ibadah_nilai'] ?? null,
                    'ibadah_predikat'         => Score::getPredikat($row['ibadah_nilai'] ?? null),
                    'kepemimpinan_nilai'      => $row['kepemimpinan_nilai'] ?? null,
                    'kepemimpinan_predikat'   => Score::getPredikat($row['kepemimpinan_nilai'] ?? null),
                ]
            );
        }

        return redirect()
            ->route('guru.penilaian.index', ['kelas_id' => $request->kelas_id])
            ->with('success', 'Semua nilai berhasil disimpan!');
    }
}
