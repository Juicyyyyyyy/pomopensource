<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->with('tasks')->get();
        return Inertia::render('App', ['projects' => $projects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = auth()->user()->projects()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'project' => $project->load('tasks'),
                'message' => 'Project created successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'project' => $project->fresh()->load('tasks'),
                'message' => 'Project updated successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }

    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Project deleted successfully.',
            ]);
        }

        return redirect()->route('projects.index');
    }
}
