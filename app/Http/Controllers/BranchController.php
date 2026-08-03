<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class BranchController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $user = $request->user();
        $branchId = (int) $validated['branch_id'];

        $hasAccess = $user->branches()->where('branches.id', $branchId)->exists()
            || (int) $user->branch_id === $branchId;

        if (! $hasAccess) {
            throw ValidationException::withMessages([
                'branch_id' => 'You do not have access to that branch.',
            ]);
        }

        $branch = Branch::query()->findOrFail($branchId);

        if (! $branch->is_active) {
            throw ValidationException::withMessages([
                'branch_id' => 'That branch is inactive.',
            ]);
        }

        $user->forceFill([
            'current_branch_id' => $branch->id,
        ])->save();

        return back()->with('success', "Switched to {$branch->name}.");
    }
}
