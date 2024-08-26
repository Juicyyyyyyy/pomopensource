<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task = $project->tasks()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'task' => $task,
                'message' => 'Task created successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'task' => $task->fresh(),
                'message' => 'Task updated successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }

    public function destroy(Request $request, Task $task)
    {
        $taskId = $task->id;
        $projectId = $task->project_id;
        $task->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'taskId' => $taskId,
                'projectId' => $projectId,
                'message' => 'Task deleted successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }
}
