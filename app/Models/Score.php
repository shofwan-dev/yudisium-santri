<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guru_id',
        'akhlak_nilai',
        'akhlak_predikat',
        'disiplin_nilai',
        'disiplin_predikat',
        'tanggung_jawab_nilai',
        'tanggung_jawab_predikat',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Helper function recommended by the user
    public static function getPredikat($nilai)
    {
        if ($nilai >= 84) return 'TB';
        if ($nilai >= 76) return 'BS';
        if ($nilai >= 68) return 'B';
        if ($nilai >= 60) return 'C';
        return 'K';
    }
}
