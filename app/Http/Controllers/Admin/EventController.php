<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('participations')
            ->latest()->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_winners' => 'required|integer|min:1|max:100',
            'rules' => 'nullable|string',
            'auto_tag' => 'nullable|string|max:50',
            'status' => 'required|in:draft,active,ended',
        ]);

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('events', 'public');
        }

        // Bersihkan auto_tag: hapus # dan spasi, lowercase
        $autoTag = $request->auto_tag
            ? strtolower(str_replace(['#', ' '], ['', ''], $request->auto_tag))
            : null;

        Event::create([
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'poster_path' => $posterPath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_winners' => $request->max_winners,
            'rules' => $request->rules,
            'auto_tag' => $autoTag,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat!');
    }

    public function show(Event $event)
    {
        $leaderboard = $event->photos()
            ->with(['files', 'user'])
            ->take(20)->get();

        $totalParticipants = $event->participations()
            ->distinct('user_id')->count();

        return view('admin.events.show', compact('event', 'leaderboard', 'totalParticipants'));
    }

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate(['status' => 'required|in:draft,active,ended']);
        $event->update(['status' => $request->status]);
        return back()->with('success', 'Status event diperbarui.');
    }

    public function destroy(Event $event)
    {
        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }
        $event->delete();
        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}