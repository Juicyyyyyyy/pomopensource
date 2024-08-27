<?php

namespace App\Http\Controllers;

use App\Models\FocusedSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FocusedSessionController extends Controller
{
    public function index(Request $request)
    {
        $focusedSessions = $request->user()->focusedSessions()
            ->with(['task', 'project'])
            ->latest()
            ->paginate(10);

        return Inertia::render('FocusedSessions/Index', [
            'focusedSessions' => $focusedSessions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'project_id' => 'nullable|exists:projects,id',
            'started_at' => 'required|date',
        ]);

        $focusedSession = $request->user()->focusedSessions()->create($validated);

        return Inertia::render('FocusedSessions/Show', [
            'focusedSession' => $focusedSession->load(['task', 'project']),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ended_at' => 'required|date',
            'time_focused' => 'required|integer',
        ]);

        $focusedSession = $request->user()->focusedSessions()
            ->whereNull('ended_at')
            ->latest()
            ->firstOrFail();

        $startTime = Carbon::parse($focusedSession->started_at);
        $endTime = Carbon::parse($validated['ended_at']);

        $minutesFocused = $startTime->diffInMinutes($endTime);

        $focusedSession->update([
            'ended_at' => $validated['ended_at'],
            'time_focused' => $validated['time_focused'],
            'minute_focused' => $minutesFocused,
        ]);


        return Redirect::back()->with([
            'success' => true,
            'focusedSession' => $focusedSession->fresh()->load(['task', 'project']),
            'message' => 'Focused session updated successfully.',
        ]);
    }
}
