<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // // Buat Pembimbing
        // $pembimbing = User::create([
        //     'name' => 'Pembimbing Demo',
        //     'email' => 'pembimbing@test.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'pembimbing',
        // ]);

        // // Buat 3 Siswa
        // $siswa1 = User::create([
        //     'name' => 'Ahmad Siswa',
        //     'email' => 'siswa1@test.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'siswa',
        // ]);

        // $siswa2 = User::create([
        //     'name' => 'Budi Pelajar',
        //     'email' => 'siswa2@test.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'siswa',
        // ]);

        // $siswa3 = User::create([
        //     'name' => 'Citra Mahasiswa',
        //     'email' => 'siswa3@test.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'siswa',
        // ]);

        // // Buat Tagihan Kos Bulan 1-4
        // for ($bulan = 1; $bulan <= 4; $bulan++) {
        //     $tagihan = Tagihan::create([
        //         'nama' => "Kos Bulan $bulan",
        //         'kategori' => 'kos',
        //         'nominal' => 300000,
        //         'bulan' => $bulan,
        //         'tenggat' => now()->addDays(30 * $bulan),
        //         'keterangan' => "Pembayaran kos untuk bulan $bulan",
        //     ]);

        //     // Generate pembayaran untuk semua siswa
        //     foreach ([$siswa1, $siswa2, $siswa3] as $siswa) {
        //         Pembayaran::create([
        //             'user_id' => $siswa->id,
        //             'tagihan_id' => $tagihan->id,
        //             'nama_tagihan' => $tagihan->nama,
        //             'kategori' => $tagihan->kategori,
        //             'nominal' => $tagihan->nominal,
        //             'bulan' => $tagihan->bulan,
        //             'tenggat' => $tagihan->tenggat,
        //             'status' => 'belum_bayar',
        //         ]);
        //     }
        // }

        // // Buat Tagihan Alat Praktik Bulan 1-4
        // for ($bulan = 1; $bulan <= 4; $bulan++) {
        //     $tagihan = Tagihan::create([
        //         'nama' => "Alat Praktik Bulan $bulan",
        //         'kategori' => 'alat_praktik',
        //         'nominal' => 150000,
        //         'bulan' => $bulan,
        //         'tenggat' => now()->addDays(30 * $bulan),
        //         'keterangan' => "Pembayaran alat praktik untuk bulan $bulan",
        //     ]);

        //     // Generate pembayaran untuk semua siswa
        //     foreach ([$siswa1, $siswa2, $siswa3] as $siswa) {
        //         Pembayaran::create([
        //             'user_id' => $siswa->id,
        //             'tagihan_id' => $tagihan->id,
        //             'nama_tagihan' => $tagihan->nama,
        //             'kategori' => $tagihan->kategori,
        //             'nominal' => $tagihan->nominal,
        //             'bulan' => $tagihan->bulan,
        //             'tenggat' => $tagihan->tenggat,
        //             'status' => 'belum_bayar',
        //         ]);
        //     }
        // }

        // // Simulasi beberapa pembayaran dengan status berbeda
        // // Siswa 1 - Bayar Kos Bulan 1 (Pending)
        // $p1 = Pembayaran::where('user_id', $siswa1->id)
        //     ->where('kategori', 'kos')
        //     ->where('bulan', 1)
        //     ->first();
        // $p1->update([
        //     'metode' => 'transfer',
        //     'status' => 'pending',
        //     'tanggal_bayar' => now(),
        // ]);

        // // Siswa 2 - Bayar Kos Bulan 1 (Diterima)
        // $p2 = Pembayaran::where('user_id', $siswa2->id)
        //     ->where('kategori', 'kos')
        //     ->where('bulan', 1)
        //     ->first();
        // $p2->update([
        //     'metode' => 'cash',
        //     'status' => 'diterima',
        //     'tanggal_bayar' => now()->subDays(1),
        // ]);

        // echo "Demo data created!\n";
        // echo "Pembimbing: pembimbing@test.com / password\n";
        // echo "Siswa 1: siswa1@test.com / password\n";
        // echo "Siswa 2: siswa2@test.com / password\n";
        // echo "Siswa 3: siswa3@test.com / password\n";
    }
}