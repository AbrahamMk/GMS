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

        $classesQuery = GymClass::withoutGlobalScopes()->with(['sessions', 'trainer'])->latest();
        $trainersQuery = Trainer::withoutGlobalScopes()->where('is_active', true);

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

        $validated['branch_id'] = $branchContext->branch()?->id ?? 1;

        GymClass::withoutGlobalScopes()->create($validated);

        return redirect()->back()->with('success', 'Class created successfully.');
    }

    public function update(Request $request, int $gymClass)
    {
        $gymClassModel = GymClass::withoutGlobalScopes()->findOrFail($gymClass);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $gymClassModel->update($validated);

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    public function destroy(int $gymClass)
    {
        $gymClassModel = GymClass::withoutGlobalScopes()->findOrFail($gymClass);
        $gymClassModel->delete();

        return redirect()->back()->with('success', 'Class deleted successfully.');
    }
}
