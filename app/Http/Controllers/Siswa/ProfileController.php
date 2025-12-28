<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        // Mengambil data user yang sedang login beserta relasi data siswanya
        $user = Auth::user();
        $siswa = $user->siswa; // Pastikan relasi 'siswa' sudah ada di Model User

        return view('siswa.profile', compact('user', 'siswa'));
    }
}