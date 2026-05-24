<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function dashboard()
    {
        $photos = Photo::where('status', 'public')
            ->with(['user', 'likes', 'saves', 'comments'])
            ->latest()
            ->get();
        $bgPhoto = Photo::where('status', 'publik')
            ->inRandomOrder()
            ->first();
        return view('dashboard', compact('photos'));

    }

    public function showUpload()
    {
        return view('upload');
    }

    public function download(Photo $photo)
    {
        if ($photo->status == 'private' && $photo->user_id !== auth()->id()) {
            abort(403, 'Foto ini private.');
        }
        $path = Storage::disk('public')->path($photo->file_path);
        $filename = $photo->caption . '.' . pathinfo($path, PATHINFO_EXTENSION);
        return response()->download($path, $filename);
    }


    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:20120',

            'caption' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:public,private',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        Photo::create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'caption' => $request->caption,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Foto berhasil diupload!');
    }


    //Admin hapus foto
    public function destroy(Photo $photo)
    {


        if ($photo->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        Storage::disk('public')->delete($photo->file_path);

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    //Show Sharelink
    public function show(Photo $photo)
    {
        if ($photo->status == 'private' && $photo->user_id !== auth()->id()) {
            abort(403);
        }

        $sessionKey = 'viewed_photo' . $photo->id;
        if (!session()->has($sessionKey)) {
            $photo->increment('views');
            session()->put($sessionKey, true);
        }
        $photo->load(['user', 'likes', 'saves', 'comments.user']);
        return view('photos.show', compact('photo'));
    }

}