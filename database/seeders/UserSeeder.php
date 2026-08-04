<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $universitas = [
            'Universitas Dian Nuswantoro',
            'Universitas Diponegoro',
            'Universitas Negeri Semarang',
            'Universitas Gadjah Mada',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Brawijaya',
            'Universitas Trunojoyo Madura',
        ];

        $jurusan = [
            'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komunikasi',
            'Manajemen', 'Akuntansi', 'Desain Komunikasi Visual',
            'Teknik Elektro', 'Psikologi',
        ];

        $daftarMinat = ['web', 'ui-ux', 'data', 'mobile', 'bisnis', 'desain', 'penelitian', 'sosial'];
        $daftarSkill = ['html-css', 'javascript', 'php', 'python', 'figma', 'excel', 'public-speaking', 'menulis'];

        // ---- Akun demo untuk juri: ADMIN ----
        $admin = User::create([
            'name'     => 'Admin StudentHub',
            'email'    => 'admin@studenthub.test',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $admin->profile()->create([
            'universitas' => 'Universitas Dian Nuswantoro',
            'jurusan'     => 'Teknik Informatika',
            'semester'    => 7,
            'minat'       => ['web', 'data'],
            'skill'       => ['php', 'javascript'],
            'preferensi'  => ['format' => 'keduanya', 'biaya' => 'keduanya'],
        ]);

        // ---- Akun demo untuk juri: MAHASISWA ----
        $demo = User::create([
            'name'     => 'Sari Wulandari',
            'email'    => 'mahasiswa@studenthub.test',
            'password' => 'password',
            'role'     => 'mahasiswa',
        ]);

        $demo->profile()->create([
            'universitas' => 'Universitas Dian Nuswantoro',
            'jurusan'     => 'Teknik Informatika',
            'semester'    => 5,
            'minat'       => ['web', 'ui-ux', 'data'],
            'skill'       => ['html-css', 'javascript', 'figma'],
            'preferensi'  => ['format' => 'keduanya', 'biaya' => 'gratis'],
        ]);

        // ---- 12 mahasiswa lain sebagai kontributor ----
        User::factory(12)->create(['role' => 'mahasiswa'])
            ->each(function (User $user) use ($universitas, $jurusan, $daftarMinat, $daftarSkill) {
                $user->profile()->create([
                    'universitas' => fake()->randomElement($universitas),
                    'jurusan'     => fake()->randomElement($jurusan),
                    'semester'    => fake()->numberBetween(1, 8),
                    'minat'       => fake()->randomElements($daftarMinat, 3),
                    'skill'       => fake()->randomElements($daftarSkill, fake()->numberBetween(2, 4)),
                    'preferensi'  => [
                        'format' => fake()->randomElement(['online', 'offline', 'keduanya']),
                        'biaya'  => fake()->randomElement(['gratis', 'keduanya']),
                    ],
                ]);
            });
    }
}
