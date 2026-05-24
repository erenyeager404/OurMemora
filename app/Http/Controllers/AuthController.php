<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== REGISTER =====

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            // ↑ Hapus unique:users dari sini
            // Kita handle sendiri di bawah agar lebih fleksibel
            'password' => 'required|min:6|confirmed',
        ]);

        // Cek apakah email sudah terdaftar
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            if ($existingUser->hasVerifiedEmail()) {
                // Email sudah terdaftar DAN sudah diverifikasi
                // → Tolak registrasi, suruh login
                return redirect()->route('landing')->withErrors([
                    'email' => 'Email ini sudah terdaftar. Silahkan login.'
                ])->withInput($request->only('name', 'email'));
            } else {
                // Email sudah terdaftar TAPI belum diverifikasi
                // → Hapus akun lama, buat akun baru dengan data terbaru
                $existingUser->delete();
                // ↑ Hapus akun lama yang belum terverifikasi
                // Anggap akun itu tidak valid karena tidak pernah dikonfirmasi
            }
        }

        // Buat akun baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silahkan cek email untuk verifikasi.');
    }

    // ===== LOGIN =====

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            // Cek verifikasi email
            if (!auth()->user()->hasVerifiedEmail()) {



                return redirect()->route('verification.notice')
                    ->with('warning', 'Email kamu belum diverifikasi. Cek inbox kamu.');
            }

            // Update waktu login terakhir
            auth()->user()->update(['last_login_at' => now()]);

            // Redirect sesuai role
            if (auth()->user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return redirect()->route('landing')->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    // ===== LOGOUT =====

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}