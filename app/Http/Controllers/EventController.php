<?php
namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $activeEvents = Event::whereIn('status', ['active', 'voting'])
            ->withCount('participations')
            ->orderBy('end_date')
            ->get();

        $endedEvents = Event::where('status', 'ended')
            ->withCount('participations')
            ->latest('end_date')
            ->take(6)
            ->get();

        $upcomingEvents = Event::where('status', 'active')
            ->where('start_date', '>', now())
            ->withCount('participations')
            ->orderBy('start_date')
            ->get();

        return view('events.index', compact('activeEvents', 'endedEvents', 'upcomingEvents'));
    }

    public function show(Event $event)
    {
        // Draft hanya admin
        if ($event->status === 'draft' && !auth()->user()?->is_admin) {
            abort(404);
        }

        $leaderboard = $event->getLeaderboard(20);
        $totalParticipants = $event->participations()->distinct('user_id')->count();
        $userHasJoined = auth()->check() ? $event->hasUserJoined(auth()->id()) : false;
        $userPhoto = null;

        if ($userHasJoined) {
            $p = $event->participations()->where('user_id', auth()->id())->first();
            $userPhoto = $p?->photo?->load('files');
        }

        return view('events.show', compact(
            'event',
            'leaderboard',
            'totalParticipants',
            'userHasJoined',
            'userPhoto'
        ));
    }
}