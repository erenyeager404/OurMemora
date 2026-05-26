<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Halaman profile user
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->photos()
            ->with(['files', 'likes', 'comments', 'tags']);

        match ($request->get('sort', 'newest')) {
            'oldest' => $query->oldest(),
            'public' => $query->where('status', 'public')->latest(),
            'private' => $query->where('status', 'private')->latest(),
            default => $query->latest(),
        };

        $photos = $query->get();

        return view('profile.index', compact('user', 'photos'));
    }

    /**
     * Halaman ubah password
     */
    public function changePasswordPage()
    {
        return view('profile.change-password');
    }

    /**
     * Proses ubah password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak cocok.'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Update avatar user
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();

        // Upload avatar baru
        $path = $request->file('avatar')->store('avatars', 'public');

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Simpan path avatar baru
        $user->update([
            'avatar' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar berhasil diperbarui.',
            'url' => asset('storage/' . $path),
        ]);
    }
}