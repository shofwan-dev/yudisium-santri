<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exports\GurusTemplateExport;
use App\Imports\GurusImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('guru');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $gurus = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.gurus.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.gurus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Username/Email sudah digunakan.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $user->assignRole($guruRole);

        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(User $guru)
    {
        return view('admin.gurus.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($guru->id),
            ],
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Username/Email sudah digunakan.'
        ]);

        $guru->name = $request->name;
        $guru->email = $request->email;
        if ($request->filled('password')) {
            $guru->password = bcrypt($request->password);
        }
        $guru->save();

        return redirect()->route('admin.gurus.index')->with('success', 'Data Guru berhasil diupdate.');
    }

    public function destroy(User $guru)
    {
        $guru->delete();
        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $filename = 'template-guru-' . date('Ymd-His') . '.xlsx';
        return Excel::download(new GurusTemplateExport(), $filename);
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
            // Use native PHP file handling to avoid
            // Laravel FilesystemAdapter "Path cannot be empty" on Windows
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return redirect()->route('admin.gurus.index')
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
                return redirect()->route('admin.gurus.index')
                    ->with('error', 'Gagal menyimpan file sementara.');
            }

            $import = new GurusImport();
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

            return redirect()->route('admin.gurus.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.gurus.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}

