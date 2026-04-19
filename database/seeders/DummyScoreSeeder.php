<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Score;

class DummyScoreSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();
        $gurus = User::role('guru')->get();

        foreach ($students as $student) {
            foreach ($gurus as $guru) {
                // Generate random scores between 60 and 95
                $akhlak = rand(60, 95);
                $disiplin = rand(60, 95);
                $tj = rand(60, 95);

                Score::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'guru_id' => $guru->id,
                    ],
                    [
                        'akhlak_nilai' => $akhlak,
                        'akhlak_predikat' => Score::getPredikat($akhlak),
                        'disiplin_nilai' => $disiplin,
                        'disiplin_predikat' => Score::getPredikat($disiplin),
                        'tanggung_jawab_nilai' => $tj,
                        'tanggung_jawab_predikat' => Score::getPredikat($tj),
                    ]
                );
            }
        }
    }
}
