<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassRoom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures, Importable;

    protected $updatedCount = 0;
    protected $createdCount = 0;
    protected $skippedCount = 0;
    protected $classCache   = [];

    /**
     * Resolve kelas_id from nama_kelas, with caching.
     */
    private function resolveKelasId(?string $namaKelas): ?int
    {
        if (!$namaKelas) return null;

        $namaKelas = trim($namaKelas);

        if (isset($this->classCache[$namaKelas])) {
            return $this->classCache[$namaKelas];
        }

        $class = ClassRoom::where('nama_kelas', $namaKelas)->first();
        $this->classCache[$namaKelas] = $class?->id;

        return $class?->id;
    }

    public function model(array $row)
    {
        $id       = $row['id'] ?? null;
        $no       = $row['no'] ?? null;
        $nama     = $row['nama'] ?? null;
        $kelas    = $row['kelas'] ?? null;
        $kelasId  = $this->resolveKelasId($kelas);

        if (!$nama) {
            $this->skippedCount++;
            return null;
        }

        // If no valid class found, skip
        if (!$kelasId) {
            $this->skippedCount++;
            return null;
        }

        // Try to find existing student by ID
        if ($id) {
            $student = Student::find($id);
            if ($student) {
                $student->update([
                    'no'       => $no,
                    'nama'     => $nama,
                    'kelas_id' => $kelasId,
                ]);
                $this->updatedCount++;
                return null; // Already saved via update
            }
        }

        // Create new student
        $this->createdCount++;
        return new Student([
            'no'       => $no,
            'nama'     => $nama,
            'kelas_id' => $kelasId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama'  => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
        ];
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
