<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Exports\StudentsExport;
use App\Exports\StudentDetailExport;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    // ─── Direktori penyimpanan foto ───────────────────────────────────────────
    // Simpan langsung di public/uploads/students/ agar tanpa symlink
    private const FOTO_DIR = 'uploads/students';

    /**
     * Simpan foto menggunakan PHP native (bypass Laravel Storage abstraction)
     * Return: relative path dari public_path(), e.g. 'uploads/students/xxx.jpg'
     *         atau null jika gagal / tidak ada file
     */
    private function saveFoto(): ?string
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpFile  = $_FILES['foto']['tmp_name'];
        $origName = $_FILES['foto']['name'];
        $mimeType = $_FILES['foto']['type'];
        $fileSize = $_FILES['foto']['size'];

        // Validasi
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowed) || $fileSize > 2 * 1024 * 1024) {
            return null;
        }
        if (!is_uploaded_file($tmpFile)) {
            return null;
        }

        // Ekstensi
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (empty($ext)) {
            $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png',
                    'image/gif' => 'gif', 'image/webp' => 'webp'][$mimeType] ?? 'jpg';
        }

        // Buat folder jika belum ada
        $dir = public_path(self::FOTO_DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = 'student_' . time() . '_' . uniqid() . '.' . $ext;
        $dest     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpFile, $dest)) {
            return null;
        }

        return self::FOTO_DIR . '/' . $filename;
    }

    /**
     * Hapus file foto dari disk
     */
    private function deleteFoto(?string $path): void
    {
        if (!$path) return;

        // Coba path baru (public/uploads/students/)
        $newPath = public_path($path);
        if (file_exists($newPath)) {
            @unlink($newPath);
            return;
        }

        // Fallback: path lama (storage/app/public/students/)
        $oldPath = storage_path('app/public/' . $path);
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Student::with(['classRoom', 'scores.guru']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $students = $query->orderBy('kelas_id')->orderBy('no')->paginate(15)->withQueryString();
        $classes  = ClassRoom::all();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no'       => 'nullable|string|max:20',
            'nama'     => 'required|string|max:255',
            'kelas_id' => 'required|exists:classes,id',
        ]);

        $data = $request->only(['no', 'nama', 'kelas_id']);

        $fotoPath = $this->saveFoto();
        if ($fotoPath) {
            $data['foto'] = $fotoPath;
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Santri berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['classRoom', 'scores.guru']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'no'       => 'nullable|string|max:20',
            'nama'     => 'required|string|max:255',
            'kelas_id' => 'required|exists:classes,id',
        ]);

        $data = $request->only(['no', 'nama', 'kelas_id']);

        $fotoPath = $this->saveFoto();
        if ($fotoPath) {
            // Hapus foto lama
            $this->deleteFoto($student->foto);
            $data['foto'] = $fotoPath;
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $this->deleteFoto($student->foto);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Santri berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $kelasId  = $request->kelas_id ?: null;
        $filename = 'data-santri-' . date('Ymd-His') . '.xlsx';

        return Excel::download(new StudentsExport($kelasId), $filename);
    }

    public function exportSingle(Student $student)
    {
        $filename = 'detail-santri-' . \Str::slug($student->nama) . '-' . date('Ymd-His') . '.xlsx';
        return Excel::download(new StudentDetailExport($student->id), $filename);
    }

    public function downloadTemplate()
    {
        $filename = 'template-santri-' . date('Ymd-His') . '.xlsx';
        return Excel::download(new StudentsTemplateExport(), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            // Use native PHP file handling (same approach as saveFoto)
            // to avoid Laravel FilesystemAdapter "Path cannot be empty" on Windows
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return redirect()->route('admin.students.index')
                    ->with('error', 'Gagal upload file.');
            }

            $tmpFile  = $_FILES['file']['tmp_name'];
            $origName = $_FILES['file']['name'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0775, true);
            }

            $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'import_' . uniqid() . '.' . $ext;

            if (!move_uploaded_file($tmpFile, $tempFile)) {
                return redirect()->route('admin.students.index')
                    ->with('error', 'Gagal menyimpan file sementara.');
            }

            $import = new StudentsImport();
            Excel::import($import, $tempFile);

            // Clean up temp file
            @unlink($tempFile);

            $updated = $import->getUpdatedCount();
            $created = $import->getCreatedCount();
            $skipped = $import->getSkippedCount();
            $failures = $import->failures();

            $message = "Import selesai! Diperbarui: {$updated}, Ditambahkan: {$created}";
            if ($skipped > 0) {
                $message .= ", Dilewati: {$skipped}";
            }
            if ($failures->count() > 0) {
                $message .= ", Gagal validasi: {$failures->count()} baris";
            }

            return redirect()->route('admin.students.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}

