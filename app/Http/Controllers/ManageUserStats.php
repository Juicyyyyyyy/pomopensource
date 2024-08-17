<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Stats;

class ManageUserStats extends Controller
{
    /**
     * Calculate total time studied and update time_studied value.
     *
     * @return void
     */
    public function updateTimeFocused(Request $request): void
    {
        $request->user()->updateTimeFocusedProject();
        $userProjects = $request->user()->projects();

        $totalTimeFocused = $userProjects->sum('time_focused_total');
        $request->user()->stats()->updateTimeFocused($totalTimeFocused);

    }

    // func: Calculate hoursFocused

    // func: Calculate daysAccessed

    // func: Calculate day streak

    // func: Display calendar charts
}
