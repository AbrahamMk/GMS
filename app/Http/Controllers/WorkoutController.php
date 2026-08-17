<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberWorkoutAssignment;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Scopes\BranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutController extends Controller
{
    public function index(): Response
    {
        $plans = WorkoutPlan::withoutGlobalScopes()
            ->withCount('assignments')
            ->with('exercises')
            ->latest()
            ->get()
            ->map(fn (WorkoutPlan $plan): array => [
                'id'               => $plan->id,
                'name'             => $plan->name,
                'description'      => $plan->description,
                'category'         => $plan->category,
                'difficulty'       => $plan->difficulty,
                'duration_minutes' => $plan->duration_minutes,
                'calories_est'     => $plan->calories_est,
                'target_muscle'    => $plan->target_muscle,
                'assigned_count'   => $plan->assignments_count,
                'exercises'        => $plan->exercises->map(fn (WorkoutPlanExercise $ex): array => [
                    'id'          => $ex->id,
                    'name'        => $ex->name,
                    'sets'        => $ex->sets,
                    'reps'        => $ex->reps,
                    'rest_seconds'=> $ex->rest_seconds,
                    'weight_note' => $ex->weight_note,
                    'day_label'   => $ex->day_label,
                    'sort_order'  => $ex->sort_order,
                ])->values(),
            ])
            ->values();

        $members = Member::withoutGlobalScope(BranchScope::class)
            ->select('id', 'first_name', 'last_name', 'member_code')
            ->where('status', 'active')
            ->get();

        return Inertia::render('Workouts/Index', [
            'workoutPlans' => $plans,
            'members'      => $members,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'required|string|max:100',
            'difficulty'       => 'required|string|max:50',
            'duration_minutes' => 'nullable|integer|min:5|max:300',
            'calories_est'     => 'nullable|integer|min:0',
            'target_muscle'    => 'nullable|string|max:255',
            'exercises'        => 'nullable|array',
            'exercises.*.name'        => 'required|string|max:255',
            'exercises.*.sets'        => 'required|integer|min:1',
            'exercises.*.reps'        => 'required|string|max:50',
            'exercises.*.rest_seconds'=> 'nullable|string|max:50',
            'exercises.*.weight_note' => 'nullable|string|max:100',
            'exercises.*.day_label'   => 'nullable|string|max:100',
        ]);

        $plan = WorkoutPlan::create([
            'branch_id'           => 1,
            'name'                => $validated['name'],
            'description'         => $validated['description'] ?? null,
            'category'            => $validated['category'],
            'difficulty'          => $validated['difficulty'],
            'duration_minutes'    => $validated['duration_minutes'] ?? 45,
            'calories_est'        => $validated['calories_est'] ?? null,
            'target_muscle'       => $validated['target_muscle'] ?? null,
            'created_by_user_id'  => $request->user()?->id,
        ]);

        foreach (($validated['exercises'] ?? []) as $i => $ex) {
            WorkoutPlanExercise::create([
                'workout_plan_id' => $plan->id,
                'name'            => $ex['name'],
                'sets'            => $ex['sets'],
                'reps'            => $ex['reps'],
                'rest_seconds'    => $ex['rest_seconds'] ?? null,
                'weight_note'     => $ex['weight_note'] ?? null,
                'day_label'       => $ex['day_label'] ?? null,
                'sort_order'      => $i,
            ]);
        }

        return back()->with('success', 'Workout plan created successfully.');
    }

    public function update(Request $request, int $workoutPlan): RedirectResponse
    {
        $plan = WorkoutPlan::withoutGlobalScopes()->findOrFail($workoutPlan);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'required|string|max:100',
            'difficulty'       => 'required|string|max:50',
            'duration_minutes' => 'nullable|integer|min:5|max:300',
            'calories_est'     => 'nullable|integer|min:0',
            'target_muscle'    => 'nullable|string|max:255',
            'exercises'        => 'nullable|array',
            'exercises.*.name'        => 'required|string|max:255',
            'exercises.*.sets'        => 'required|integer|min:1',
            'exercises.*.reps'        => 'required|string|max:50',
            'exercises.*.rest_seconds'=> 'nullable|string|max:50',
            'exercises.*.weight_note' => 'nullable|string|max:100',
            'exercises.*.day_label'   => 'nullable|string|max:100',
        ]);

        $plan->update([
            'name'             => $validated['name'],
            'description'      => $validated['description'] ?? null,
            'category'         => $validated['category'],
            'difficulty'       => $validated['difficulty'],
            'duration_minutes' => $validated['duration_minutes'] ?? $plan->duration_minutes,
            'calories_est'     => $validated['calories_est'] ?? null,
            'target_muscle'    => $validated['target_muscle'] ?? null,
        ]);

        // Replace exercises
        $plan->exercises()->delete();
        foreach (($validated['exercises'] ?? []) as $i => $ex) {
            WorkoutPlanExercise::create([
                'workout_plan_id' => $plan->id,
                'name'            => $ex['name'],
                'sets'            => $ex['sets'],
                'reps'            => $ex['reps'],
                'rest_seconds'    => $ex['rest_seconds'] ?? null,
                'weight_note'     => $ex['weight_note'] ?? null,
                'day_label'       => $ex['day_label'] ?? null,
                'sort_order'      => $i,
            ]);
        }

        return back()->with('success', 'Workout plan updated.');
    }

    public function destroy(int $workoutPlan): RedirectResponse
    {
        WorkoutPlan::withoutGlobalScopes()->findOrFail($workoutPlan)->delete();
        return back()->with('success', 'Workout plan deleted.');
    }

    public function assign(Request $request, int $workoutPlan): RedirectResponse
    {
        $plan = WorkoutPlan::withoutGlobalScopes()->findOrFail($workoutPlan);

        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'notes'     => 'nullable|string|max:500',
        ]);

        // Deactivate existing active assignment for this member
        MemberWorkoutAssignment::where('member_id', $validated['member_id'])
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create new assignment
        MemberWorkoutAssignment::create([
            'member_id'       => $validated['member_id'],
            'workout_plan_id' => $plan->id,
            'assigned_at'     => now(),
            'notes'           => $validated['notes'] ?? null,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Workout plan assigned to member.');
    }

    public function unassign(Request $request, int $workoutPlan): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        MemberWorkoutAssignment::where('member_id', $validated['member_id'])
            ->where('workout_plan_id', $workoutPlan)
            ->update(['is_active' => false]);

        return back()->with('success', 'Assignment removed.');
    }
}
