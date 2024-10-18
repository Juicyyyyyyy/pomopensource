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

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task->update($validated);
    }

    public function destroy(Request $request, Task $task)
    {
        $taskId = $task->id;
        $projectId = $task->project_id;
        $task->delete();
    }
}
