<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $activeEvents = Event::where('status', 'active')
            ->withCount('participations')
            ->orderBy('end_date')
            ->get();

        $endedEvents = Event::where('status', 'ended')
            ->withCount('participations')
            ->latest('end_date')
            ->take(6)
            ->get();

        return view('events.index', compact('activeEvents', 'endedEvents'));
    }

    public function show(Event $event)
    {
        $leaderboard = $event->photos()
            ->with(['files', 'user'])
            ->take(20)
            ->get();
        // Top 20 foto berdasarkan jumlah like

        $totalParticipants = $event->participations()
            ->distinct('user_id')
            ->count();

        return view('events.show', compact('event', 'leaderboard', 'totalParticipants'));
    }
}