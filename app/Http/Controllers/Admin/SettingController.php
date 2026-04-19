<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Score;
use App\Models\Student;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_title'    => AppSetting::getValue('app_title', 'Yudisium Santri'),
            'app_subtitle' => AppSetting::getValue('app_subtitle', 'Angkatan ke-32 Tahun 2026'),
            'app_logo'     => AppSetting::getValue('app_logo', null),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_title'    => 'required|string|max:255',
            'app_subtitle' => 'nullable|string|max:255',
        ]);

        // ── Simpan judul & sub-judul ───────────────────────────────
        AppSetting::setValue('app_title',    $request->app_title);
        AppSetting::setValue('app_subtitle', $request->app_subtitle ?? '');

        // ── Upload logo menggunakan PHP native ($_FILES) ───────────
        // Bypass semua Laravel file abstraction (getRealPath() = '' di Windows Laragon)
        if (isset($_FILES['app_logo']) && $_FILES['app_logo']['error'] === UPLOAD_ERR_OK) {

            $tmpFile  = $_FILES['app_logo']['tmp_name'];   // e.g. C:\Windows\Temp\phpXXXX
            $origName = $_FILES['app_logo']['name'];        // e.g. logo.png
            $mimeType = $_FILES['app_logo']['type'];        // e.g. image/png
            $fileSize = $_FILES['app_logo']['size'];        // bytes

            // ── Validasi dasar ─────────────────────────────────────
            $allowedMime = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize     = 2 * 1024 * 1024; // 2 MB

            if (!in_array($mimeType, $allowedMime)) {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'Format logo tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
            }

            if ($fileSize > $maxSize) {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'Ukuran logo melebihi 2 MB.');
            }

            if (!is_uploaded_file($tmpFile)) {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'File upload tidak valid.');
            }

            // ── Tentukan ekstensi ──────────────────────────────────
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (empty($ext)) {
                $extMap = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp',
                ];
                $ext = $extMap[$mimeType] ?? 'jpg';
            }

            // ── Buat direktori jika belum ada ──────────────────────
            $destDir = public_path('uploads/settings');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }

            // ── Hapus logo lama ────────────────────────────────────
            $oldLogo = AppSetting::getValue('app_logo');
            if ($oldLogo) {
                $oldFile = public_path($oldLogo);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // ── Pindahkan file ke public/uploads/settings/ ─────────
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
            $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;

            if (!move_uploaded_file($tmpFile, $destPath)) {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'Gagal menyimpan logo. Periksa permission folder public/uploads/.');
            }

            // ── Simpan path relatif ke DB (relative to public/) ────
            $logoPath = 'uploads/settings/' . $filename;
            AppSetting::setValue('app_logo', $logoPath);

        } elseif (isset($_FILES['app_logo']) && $_FILES['app_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Ada error upload selain "tidak ada file"
            $errCodes = [
                UPLOAD_ERR_INI_SIZE   => 'File melebihi batas upload_max_filesize di php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'File melebihi batas MAX_FILE_SIZE di form.',
                UPLOAD_ERR_PARTIAL    => 'File hanya terupload sebagian.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temp tidak ditemukan.',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
            ];
            $errMsg = $errCodes[$_FILES['app_logo']['error']] ?? 'Error upload tidak diketahui.';
            return redirect()->route('admin.settings.index')->with('error', $errMsg);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan! ✅');
    }

    public function deleteLogo()
    {
        $logo = AppSetting::getValue('app_logo');
        if ($logo) {
            $fullPath = public_path($logo);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
        AppSetting::setValue('app_logo', null);
        return redirect()->route('admin.settings.index')->with('success', 'Logo berhasil dihapus.');
    }

    public function resetScores()
    {
        Score::truncate();
        return redirect()->route('admin.settings.index')->with('success', 'Semua data nilai berhasil dihapus secara permanen.');
    }

    public function resetStudents()
    {
        // First delete all scores to avoid foreign key constraints if they aren't cascading, though truncate doesn't care if no cascading is setup, but let's be safe. Wait, truncate on tables with foreign keys might fail in some DBs without disabling checks. Let's use delete() or truncate depending on what's better. Or disable FK checks.
        // Let's use Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Score::truncate();
        Student::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        
        return redirect()->route('admin.settings.index')->with('success', 'Semua data siswa beserta nilainya berhasil dihapus secara permanen.');
    }
}
