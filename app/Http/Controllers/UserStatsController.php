<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Stats;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserStatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = $this->updateStats($user);

        return response()->json([
            'stats' => [
                'hours_focused' => round($stats->minute_focused / 60, 1),
                'days_accessed' => $stats->days_accessed,
                'day_streak' => $stats->day_streak,
            ],
        ]);
    }

    public function updateStats(User $user)
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

        return $stats;
    }

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

    public function getCalendarData(Request $request, $year, $month = null, $day = null)
    {
        $user = $request->user();
        $stats = $this->updateStats($user);
        $view = $request->query('view', 'month');

        switch ($view) {
            case 'week':
                return $this->getWeekCalendarData($user, $year, $month, $day, $stats);
            case 'month':
                return $this->getMonthCalendarData($user, $year, $month, $stats);
            case 'year':
                return $this->getYearCalendarData($user, $year, $stats);
            default:
                return response()->json(['error' => 'Invalid view'], 400);
        }
    }

    private function getWeekCalendarData($user, $year, $month, $day, $stats)
    {
        $startDate = Carbon::create($year, $month, $day)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        return $this->getCalendarDataForDateRange($user, $startDate, $endDate, $stats);
    }

    private function getMonthCalendarData($user, $year, $month, $stats)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return $this->getCalendarDataForDateRange($user, $startDate, $endDate, $stats);
    }

    private function getYearCalendarData($user, $year, $stats)
    {
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        $focusedSessions = $user->focusedSessions()
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($session) {
                return $session->started_at->format('Y-m');
            });

        $calendar = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $sessions = $focusedSessions->get($monthKey, collect());

            $calendar[] = [
                'date' => $monthKey,
                'has_session' => $sessions->isNotEmpty(),
                'minutes_focused' => $sessions->sum('minute_focused'),
            ];
        }

        return response()->json([
            'calendar' => $calendar,
            'currentStreak' => $stats->day_streak,
            'initialYear' => (int)$year,
        ]);
    }

    private function getCalendarDataForDateRange($user, $startDate, $endDate, $stats)
    {
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

        return response()->json([
            'calendar' => $calendar,
            'currentStreak' => $stats->day_streak,
            'initialYear' => (int)$startDate->year,
            'initialMonth' => (int)$startDate->month,
            'initialDay' => (int)$startDate->day,
        ]);
    }

    public function getProjectStats(Request $request)
    {
        $projects = $request->user()->projects()->with(['tasks' => function ($query) {
            $query->withSum('focusedSessions', 'minute_focused');
        }, 'focusedSessions' => function ($query) {
            $query->selectRaw('project_id, SUM(minute_focused) as total_time')
                ->groupBy('project_id');
        }])->get();

        return response()->json([
            'projects' => $projects->map(function ($project) {
                $totalTimeOnProject = $project->focusedSessions->sum('total_time');
                $totalTimeOnTasks = $project->tasks->sum('focused_sessions_sum_minute_focused');

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'total_time_focused' => $totalTimeOnProject + $totalTimeOnTasks,
                    'tasks' => $project->tasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'name' => $task->name,
                            'time_focused' => $task->focused_sessions_sum_minute_focused ?? 0,
                        ];
                    }),
                ];
            }),
        ]);
    }






}
