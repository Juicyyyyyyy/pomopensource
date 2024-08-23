<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Stats;

class UserStatsController extends Controller
{
    /**
     * Calculate total time studied and update time_studied value.
     *
     * @param Request $request
     * @return void
     */
    public function updateTimeFocused(User $user): void
    {
        $user->projects()->each(function($project) {
            $project->updateTimeFocusedProject();
        });

        $totalTimeFocused = $user->projects()->sum('time_focused_total');
        $user->stats()->update(['time_focused' => $totalTimeFocused]);
    }

    // func: Calculate hoursFocused

    // func: Calculate daysAccessed

    // func: Calculate day streak

    // func: Display calendar charts
}
