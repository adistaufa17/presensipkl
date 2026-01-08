<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        return view('siswa.profile', compact('user', 'siswa'));
    }
}