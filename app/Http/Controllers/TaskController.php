<?php

// TaskController.php
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

        $project->tasks()->create($validated);

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Task created successfully.',
                'type' => 'success',
            ],
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task->update($validated);

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Task updated successfully.',
                'type' => 'success',
            ],
        ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Task deleted successfully.',
                'type' => 'success',
            ],
        ]);
    }
}
