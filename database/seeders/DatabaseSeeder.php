<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AppSetting;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────
        $adminRole = Role::create(['name' => 'admin']);
        $guruRole  = Role::create(['name' => 'guru']);

        // ── Users ─────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($adminRole);

        $guru = User::firstOrCreate(
            ['email' => 'guru@guru.com'],
            ['name' => 'Guru Agama', 'password' => bcrypt('password')]
        );
        $guru->assignRole($guruRole);

        $guru2 = User::firstOrCreate(
            ['email' => 'guru2@guru.com'],
            ['name' => 'Guru Akhlak', 'password' => bcrypt('password')]
        );
        $guru2->assignRole($guruRole);

        // ── Kelas ─────────────────────────────────────────────────
        $kelas1 = ClassRoom::create(['nama_kelas' => 'XII IPA 1']);
        $kelas2 = ClassRoom::create(['nama_kelas' => 'XII IPS 1']);

        // ── Santri Kelas XII IPA 1 (7 orang) ──────────────────────
        $santriIPA = [
            ['no' => '001', 'nama' => 'Ahmad Fadhilah Ramadhan'],
            ['no' => '002', 'nama' => 'Bilal Khairul Anam'],
            ['no' => '003', 'nama' => 'Dzikri Maulana Yusuf'],
            ['no' => '004', 'nama' => 'Faris Abdurrahman Hadi'],
            ['no' => '005', 'nama' => 'Ghifari Naufal Akbar'],
            ['no' => '006', 'nama' => 'Haikal Rizki Pratama'],
            ['no' => '007', 'nama' => 'Ibrahim Hakim Santoso'],
        ];

        foreach ($santriIPA as $s) {
            Student::create([
                'no'       => $s['no'],
                'nama'     => $s['nama'],
                'kelas_id' => $kelas1->id,
                'foto'     => null,
            ]);
        }

        // ── Santri Kelas XII IPS 1 (5 orang) ──────────────────────
        $santriIPS = [
            ['no' => '008', 'nama' => 'Jasim Nurul Haq'],
            ['no' => '009', 'nama' => 'Khalid Bin Walid Putra'],
            ['no' => '010', 'nama' => 'Luqman Hakim Wijaya'],
        ];

        foreach ($santriIPS as $s) {
            Student::create([
                'no'       => $s['no'],
                'nama'     => $s['nama'],
                'kelas_id' => $kelas2->id,
                'foto'     => null,
            ]);
        }

        // ── App Settings ──────────────────────────────────────────
        AppSetting::setValue('app_title', 'Yudisium Santri');
        AppSetting::setValue('app_subtitle', 'Angkatan ke-32 Tahun 2026');
    }
}
