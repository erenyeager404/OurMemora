<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Tidak bisa follow diri sendiri.'], 403);
            // ↑ Tidak boleh follow diri sendiri
        }

        $follow = Follow::where('follower_id', auth()->id())
            ->where('following_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $isFollowing = false;
        } else {
            Follow::create([
                'follower_id' => auth()->id(),
                'following_id' => $user->id,
            ]);
            $isFollowing = true;
        }

        return response()->json([
            'is_following' => $isFollowing,
            'total_followers' => $user->followers()->count(),
        ]);
    }
}