<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Guru\ScoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.students.index');
    }
    return redirect()->route('guru.penilaian.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('classes', ClassController::class);
    Route::resource('students', StudentController::class);
    Route::resource('gurus', GuruController::class);
    Route::get('/students-export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/students/{student}/export', [StudentController::class, 'exportSingle'])->name('students.export_single');
    Route::get('/students-template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/gurus-template', [GuruController::class, 'downloadTemplate'])->name('gurus.template');
    Route::post('/gurus-import', [GuruController::class, 'import'])->name('gurus.import');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/delete-logo', [SettingController::class, 'deleteLogo'])->name('settings.deleteLogo');
    Route::post('/settings/reset-scores', [SettingController::class, 'resetScores'])->name('settings.resetScores');
    Route::post('/settings/reset-students', [SettingController::class, 'resetStudents'])->name('settings.resetStudents');
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/penilaian', [ScoreController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [ScoreController::class, 'store'])->name('penilaian.store');
    Route::post('/penilaian/bulk', [ScoreController::class, 'bulkStore'])->name('penilaian.bulk');
});

require __DIR__.'/auth.php';
