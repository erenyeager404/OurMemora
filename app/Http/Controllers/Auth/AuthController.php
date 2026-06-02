<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Hapus akun yang belum terverifikasi dengan email yang sama
        $existing = User::where('email', $request->email)->first();
        if ($existing) {
            if ($existing->hasVerifiedEmail()) {
                return redirect()->route('landing')
                    ->withErrors(['email' => 'Email sudah terdaftar.'])
                    ->withInput($request->only('name', 'email'));
            }
            $existing->delete();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Cek email untuk verifikasi.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (
            !Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )
        ) {
            return redirect()->route('landing')
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Email belum diverifikasi.');
        }

        auth()->user()->update(['last_login_at' => now()]);

        return redirect()->intended(
            auth()->user()->is_admin
            ? route('admin.dashboard')
            : route('dashboard')
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail(),
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                ]
            );


            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            $user->update([
                'last_login_at' => now(),
            ]);

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route(
                $user->is_admin
                ? 'admin.dashboard'
                : 'dashboard'
            );

        } catch (\Exception $e) {
            return redirect()->route('landing')
                ->withErrors([
                    'email' => 'Login Google gagal: ' . $e->getMessage()
                ]);
        }
    }
}