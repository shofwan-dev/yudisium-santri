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
        $kelas6B = ClassRoom::create(['nama_kelas' => '6B']);
        $kelas6C = ClassRoom::create(['nama_kelas' => '6C']);
        $kelas6D = ClassRoom::create(['nama_kelas' => '6D']);

        // ── Data Santri ───────────────────────────────────────────
        $studentsData = [
            '6B' => [
                'Ahnap Muhamad Naja', 'Al Udaish', 'Andra Darmawan Pratama', 'Athalya Surya Khallyssa Jacinda',
                'Aurelya Surya Belva Damara', 'Fahmi Al Fadhilah', 'Halwa Filzah zhafira Annisa', 'Hanifa Askaryniamy',
                'Ivan Aji Pramana', 'Kanza Ayu Soleha', 'Khansa Muthmainnah Fairuz', 'M. Sandy Luthfy Mubarok',
                'Masya Nabilla Indalusia', 'Milka Aqmaira Zahwatunnisa', 'Millati Azka Hanifa', 'Moch. Fadel Al Farizi',
                'Muhammad Neder Indie', 'Muhammad Rifqy Assiddiqi', 'Muhammad Zakky Taufiqurrohman', 'Nabil Qadira Risyhad',
                'Nabila Maulida Nurul Janah', 'Novy Rismawati', 'Pradika Satrya Ramadhan', 'Rayhan Setya Pratama',
                'Satria', 'Sidik Nur Alam', 'Sylvi Rahayu', 'Zakia Zahrotul Aini'
            ],
            '6C' => [
                'A. Luhung Dhiya Pratama', 'Adrian Wasillah Al Furqon', 'Afkarrina Kamilah', 'Akbar Firmansyah',
                'Arizky Gunadi Jaya', 'Aura Al Syifa', 'Daffa Dzaki Mubarok', 'Elvina Farida', 'Febrian Ichsan Rusmawan',
                'Hafizd Muhammad Fawaz Navaro', 'Hasbi Aditya Rahman', 'Intan Nurbudiati', 'Jiyan Nasrullohilfathu',
                "M. N. Ma'arif Tobiyas", 'Mariyah Taqiyyah Hasna', 'Marwan Hafis Ilhami', 'Moh Rouf Rahman',
                'Muhammad Fakhri Fadhilah', 'Muhammad Fakhri Rizki Al Haqqi', 'Muhammad Gibran Al Khatani',
                'Muhammad Habibie Hidayat', 'Muhammad Naufal Irsyad', 'Muhammad Rafi Yazid Imansyah', 'Nauval Latif Firdaus',
                'Nayma Geida Maulana', 'Nazhif Ammar', 'Qanita Muthmainatunnisa', 'Radhidhia Mahdi Assyakha',
                'Rajwa Azhar Havilah', 'Rauf Maulana Yusuf', 'Redhas Ril Fajri', 'Refan Esa Sopian', 'Reyna Renata Salsabila',
                'Ridho Muhammad Ihsan', 'Rifani Alia Zahra', 'Salsabila Kamila Zahra', 'Sandria Arisita', 'Sigit Ardis Admaja',
                'Sutego Siko', 'Wikry Cahya Ramadhan'
            ],
            '6D' => [
                'Alfin Ardiansyah', 'Anisa Salsabila', 'Assyifa Mufidah Gunawan', 'Azizz Rizkulloh Kurniawan',
                'Elfaiza reihana Aulia', 'Habib Subhan Khoiri', 'Hasya Hisyam Rosalba', 'Itsna Alima Suparno',
                'Keisha Elviana Cyrilla', 'Leila Maulida', 'Mohammad Aidil Imam', 'Muhamad Dira Firmansyah',
                'Muhamad Zulfikri', 'Muhammad Arga Abhipraya', 'Muhammad Fathir Mubarok', 'Mutia Nabila Luthfi',
                "Najmah Nursa'adah", 'Nur Fatimah Azzahra', 'Pelangi Aulian Jiri Pangrango', 'Raeyhan Putraku Firmansyah',
                'Salsa Aulia Maharani', 'Siti Zahra Hasifa', 'Soraya Larasati Al-Rasyid', 'Talitha Izzah Afiyah S'
            ],
        ];

        $kelasMap = [
            '6B' => $kelas6B,
            '6C' => $kelas6C,
            '6D' => $kelas6D,
        ];

        $no = 1;
        foreach ($studentsData as $kelasName => $students) {
            foreach ($students as $nama) {
                Student::create([
                    'no'       => str_pad($no++, 3, '0', STR_PAD_LEFT),
                    'nama'     => $nama,
                    'kelas_id' => $kelasMap[$kelasName]->id,
                    'foto'     => null,
                ]);
            }
        }

        // ── App Settings ──────────────────────────────────────────
        AppSetting::setValue('app_title', 'Yudisium Santri');
        AppSetting::setValue('app_subtitle', 'Angkatan ke-32 Tahun 2026');
    }
}
