<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Trainer;
use App\Support\BranchContext;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminClassController extends Controller
{
    public function index(BranchContext $branchContext): Response
    {
        $branch = $branchContext->branch();

        $classesQuery = GymClass::query()->with(['sessions', 'trainer'])->latest();
        $trainersQuery = Trainer::query()->where('is_active', true);

        if ($branch && isset($branch->id)) {
            $classesQuery->where('branch_id', $branch->id);
            $trainersQuery->where('branch_id', $branch->id);
        }

        return Inertia::render('Classes/Index', [
            'classes' => $classesQuery->get(),
            'trainers' => $trainersQuery->get(),
        ]);
    }

    public function store(Request $request, BranchContext $branchContext)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['branch_id'] = $branchContext->branch()->id;

        GymClass::create($validated);

        return redirect()->back()->with('success', 'Class created successfully.');
    }

    public function update(Request $request, GymClass $gymClass)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $gymClass->update($validated);

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    public function destroy(GymClass $gymClass)
    {
        $gymClass->delete();

        return redirect()->back()->with('success', 'Class deleted successfully.');
    }
}
