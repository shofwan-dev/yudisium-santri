<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Score;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentsExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $kelasId;

    public function __construct($kelasId = null)
    {
        $this->kelasId = $kelasId;
    }

    public function view(): View
    {
        $query = Student::with(['classRoom', 'scores.guru'])->orderBy('kelas_id')->orderBy('no');

        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }

        $students = $query->get();

        return view('exports.students', compact('students'));
    }

    public function title(): string
    {
        return 'Data Santri';
    }
}
