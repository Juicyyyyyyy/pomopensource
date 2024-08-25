<?php

// ProjectController.php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->with('tasks')->get();
        return Inertia::render('Home', ['projects' => $projects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = auth()->user()->projects()->create($validated);

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Project created successfully.',
                'type' => 'success',
            ],
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update($validated);

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Project updated successfully.',
                'type' => 'success',
            ],
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return Inertia::render('Home', [
            'projects' => auth()->user()->projects()->with('tasks')->get(),
            'flash' => [
                'message' => 'Project deleted successfully.',
                'type' => 'success',
            ],
        ]);
    }
}
