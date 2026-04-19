<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $guruRole = Role::firstOrCreate(['name' => 'guru']);

        $gurus = [
            'mahrusasad' => "Prof. Dr. H. Mahrus As'ad, M.Ag.",
            'uwesqorni' => "H. Uwes Qorni, S.S., M.Pd.",
            'asislindarsono' => "Asis Lindarsono, S.Ikom., M.Pd",
            'nurbayan' => "H. Nurbayan, S.Pd., M.Ag.",
            'imamthohari' => "Drs. H. Imam Thohari, M.Pd.",
            'abunbunyamin' => "Abun Bunyamin, S.Pd.I",
            'maulanaibrahim' => "Drs. H. Maulana Ibrahim, M.Pd.",
            'izzzuddinmustofa' => "Prof. Dr. H. Izzzuddin Mustofa, MA.",
            'taufikrahman' => "Dr. H. Taufik Rahman, M.Ag.",
            'adadnursahad' => "Adad Nursahad, S.Ag.",
            'aepsaepudin' => "Aep Saepudin, M.Pd.",
            'lilisrohaeti' => "Lilis Rohaeti, S.Pd.",
            'yanyanhadiansyah' => "Yanyan Hadiansyah, S.Pd.",
            'emanhidayat' => "Eman Hidayat, S.Pd.",
            'sitisaodah' => "Siti Saodah, S.Pd.",
            'suswigi' => "Suswigi, S.Pd.I.",
            'useptrisnadi' => "Usep Trisnadi, S.Pd.I.",
            'iissuhartini' => "Iis Suhartini, S.Pd.",
            'fatimahse' => "Fatimah S.E.",
            'kaniahadiatis' => "Hj. Kania Hadiati S, S.Pd.",
            'sitihasanah' => "Siti Hasanah, M. Pd.",
            'rohmannurhakim' => "Rohman Nurhakim, S.Pd.",
            'henihendriani' => "Heni Hendriani, S.Pd.",
            'turmanakoswara' => "H. Turmana Koswara, Lc.",
            'sidikrilmantaufik' => "Sidik Rilman Taufik, S.Kom.",
            'muhammadridwan' => "Muhammad Ridwan, M.Pd.",
            'sidiqpermana' => "Sidiq Permana, S.Pd.",
            // Aep Saepudin is a duplicate name, appending '2' to username to make it unique
            'aepsaepudin2' => "Aep Saepudin, Lc. M.H.",
            'tresnarahmani' => "Tresna Rahmani, S.Pd.I.",
            'sawaludinhadi' => "Sawaludin Hadi, S.S.",
            'husnilmubarok' => "Husnil Mubarok, M.Pd.",
            'naufalandhika' => "Naufal Andhika, S.Pd",
            'muhammadmuzaiyinulhadi' => "Muhammad Muzaiyinul Hadi Lc.",
            'haelanirachmi' => "Haelani Rachmi, S.Pd.",
            'intanmeliarna' => "Intan Meliarna, S.S.",
            'rikasaris' => "Rika Sari S. Pd.",
            'irawanfaizal' => "Irawan Faizal, M.Pd.",
            'rahmathidayatulloh' => "Rahmat Hidayatulloh, S.Pd.",
            'imasmaesaroh' => "Hj. Imas Maesaroh, S. Pd. I",
            'mkhursyidhikam' => "M. Khursyid Hikam",
            'rikejunianingarumsari' => "Rike Junia Ningarumsari, S.Pd.",
            'afnifauziah' => "Afni Fauziah, S.Pd.",
            'denaahmadmaulana' => "Dena Ahmad Maulana, S.Pd.",
            'saskiamaharak' => "Saskia Maharani A K, S.Pd.",
            'rohmathulhusniyah' => "Rohmatul Husniyah, S.Pd",
            'gerigunawan' => "Geri Gunawan, S.Pd",
            'mrouufhervangga' => "M Ro'uuf Hervangga, S.Pd.",
            'ranggarustandi' => "Rangga Rustandi, S.Pd.",
            'thiaameliyahardiyanti' => "Thia Ameliya Hardiyanti, S.Pd.",
            'fahrisidiknurarif' => "Fahri Sidik Nur Arif, S.Pd.",
            'muhamadirfan' => "Muhamad Irfan",
            "ja'faralbasyar" => "Ja'far al basyar",
            'ahmadhilmialaziz' => "Ahmad Hilmi Al aziz",
            'fauzanfadhila' => "Fauzan fadhila",
            'muhammadabimaulidi' => "Muhammad Abi Maulidi",
            'nadhiyajihan' => "Nadhiya Jihan",
            'nabilazahwa' => "Nabila Zahwa, S.Pd.",
            'mutiarairfanydewi' => "Mutiara Irfany Dewi, S.Pd."
        ];

        foreach ($gurus as $username => $name) {
            $user = User::firstOrNew(['email' => $username]);
            
            // Update nama menjadi nama lengkap
            $user->name = $name;

            // Pertahankan password, hanya set jika user belum ada
            if (!$user->exists) {
                $user->password = bcrypt($username . '@123');
            }
            
            $user->save();

            if (!$user->hasRole('guru')) {
                $user->assignRole($guruRole);
            }
        }
    }
}
