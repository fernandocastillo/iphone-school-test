<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        list(,$currentBadge, $nextBadge, $nextAvailableAchivements, $remaining, $unlockedAchivements) = $user->calculate();
        //dd($unlockedAchivements);
        return response()->json([
            'unlocked_achievements' => $unlockedAchivements,
            'next_available_achievements' => $nextAvailableAchivements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remaining
        ]);
    }
}
