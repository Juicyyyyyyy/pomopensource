<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Stats;
use Inertia\Inertia;

class UserStatsController extends Controller
{

    public function updateStats(User $user): void
    {
        $stats = $user->stats ?? new Stats(['user_id' => $user->id]);

        // Update total focused time
        $this->updateTimeFocused($user, $stats);

        // Update days accessed
        $daysAccessed = $user->focusedSessions()
            ->selectRaw('DATE(started_at) as date')
            ->distinct()
            ->count();
        $stats->days_accessed = $daysAccessed;

        // Update day streak
        $this->updateDayStreak($user, $stats);

        $stats->save();
    }

    /**
     * Calculate total time studied and update time_studied value.
     *
     * @param Request $request
     * @return void
     */
    public function updateTimeFocused(User $user, Stats $stats): void
    {
        $totalMinutesFocused = $user->focusedSessions()->sum('minute_focused');
        $stats->minute_focused = $totalMinutesFocused;
    }

    private function updateDayStreak(User $user, Stats $stats): void
    {
        $lastSession = $user->focusedSessions()->latest('started_at')->first();

        if (!$lastSession) {
            $stats->day_streak = 0;
            return;
        }

        $today = Carbon::today();
        $lastSessionDate = $lastSession->started_at->startOfDay();

        if ($lastSessionDate->lt($today->subDay())) {
            // Last session was before yesterday, reset streak
            $stats->day_streak = 0;
        } elseif ($lastSessionDate->eq($today) || $lastSessionDate->eq($today->subDay())) {
            // Last session was today or yesterday, check for continuous streak
            $streak = 1;
            $checkDate = $lastSessionDate->copy()->subDay();

            while ($user->focusedSessions()->whereDate('started_at', $checkDate)->exists()) {
                $streak++;
                $checkDate->subDay();
            }

            $stats->day_streak = $streak;
        }
        // If last session was yesterday, keep the current streak
    }

    public function getMonthlyCalendar(User $user, $year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $focusedSessions = $user->focusedSessions()
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($session) {
                return $session->started_at->format('Y-m-d');
            });

        $calendar = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $sessions = $focusedSessions->get($dateString, collect());

            $calendar[] = [
                'date' => $dateString,
                'has_session' => $sessions->isNotEmpty(),
                'minutes_focused' => $sessions->sum('minute_focused'),
            ];
        }

        //return response()->json([
        //    'calendar' => $calendar,
        //    'current_streak' => $user->stats->day_streak,
        //]);
        return Inertia::render('Calendar', [
            'calendar' => $calendar,
            'currentStreak' => $user->stats->day_streak,
            'initialYear' => (int)$year,
            'initialMonth' => (int)$month,
        ]);
    }

}
