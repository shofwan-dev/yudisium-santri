<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentDetailExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $studentId;

    public function __construct($studentId)
    {
        $this->studentId = $studentId;
    }

    public function view(): View
    {
        $student = Student::with(['classRoom', 'scores.guru'])->findOrFail($this->studentId);

        return view('exports.students', [
            'students' => collect([$student])
        ]);
    }

    public function title(): string
    {
        $student = Student::findOrFail($this->studentId);
        return substr('Detail ' . $student->nama, 0, 31); // Title can't be more than 31 characters
    }
}
