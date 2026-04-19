<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'nama',
        'foto',
        'kelas_id'
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'kelas_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
