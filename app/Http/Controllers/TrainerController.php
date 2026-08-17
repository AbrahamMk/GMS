<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Support\BranchContext;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrainerController extends Controller
{
    public function index(BranchContext $branchContext): Response
    {
        $branch = $branchContext->branch();
        
        $query = Trainer::withoutGlobalScopes()->latest();

        if ($branch && isset($branch->id)) {
            $query->where('branch_id', $branch->id);
        }

        return Inertia::render('Trainers/Index', [
            'trainers' => $query->get(),
        ]);
    }

    public function show(int $trainer): Response
    {
        $trainer = Trainer::withoutGlobalScopes()->findOrFail($trainer);
        $trainer->load('sessions.gymClass');

        return Inertia::render('Trainers/Show', [
            'trainer' => $trainer,
        ]);
    }

    public function store(Request $request, BranchContext $branchContext)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:255',
            'bio'             => 'nullable|string',
            'specializations' => 'nullable|string',
            'image_url'       => 'nullable|string|max:500',
            'is_active'       => 'boolean',
        ]);

        $validated['branch_id'] = $branchContext->branch()?->id ?? 1;

        Trainer::withoutGlobalScopes()->create($validated);

        return redirect()->back()->with('success', 'Trainer created successfully.');
    }

    public function update(Request $request, int $trainer)
    {
        $trainerModel = Trainer::withoutGlobalScopes()->findOrFail($trainer);

        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:255',
            'bio'             => 'nullable|string',
            'specializations' => 'nullable|string',
            'image_url'       => 'nullable|string|max:500',
            'is_active'       => 'boolean',
        ]);

        $trainerModel->update($validated);

        return redirect()->back()->with('success', 'Trainer updated successfully.');
    }

    public function destroy(int $trainer)
    {
        $trainerModel = Trainer::withoutGlobalScopes()->findOrFail($trainer);
        $trainerModel->delete();

        return redirect()->back()->with('success', 'Trainer deleted successfully.');
    }
}
