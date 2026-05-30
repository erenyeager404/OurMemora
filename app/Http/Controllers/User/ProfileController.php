<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->photos()->with(['files', 'likes', 'comments', 'tags']);

        match ($request->get('sort', 'newest')) {
            'oldest' => $query->oldest(),
            'public' => $query->where('status', 'public')->latest(),
            'private' => $query->where('status', 'private')->latest(),
            default => $query->latest(),
        };

        $photos = $query->get();
        return view('profile.index', compact('user', 'photos'));
    }

    public function changePasswordPage()
    {
        return view('profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return redirect()->route('profile')->with('success', 'Password berhasil diubah!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        if (auth()->user()->avatar) {
            Storage::disk('public')->delete(auth()->user()->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        auth()->user()->update(['avatar' => $path]);

        return response()->json(['url' => asset('storage/' . $path)]);
    }
}