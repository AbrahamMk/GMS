<?php

namespace App\Http\Controllers;

use App\Models\ClassBooking;
use App\Models\ClassSession;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Scopes\BranchScope;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminClassController extends Controller
{
    public static function syncUpcomingSessionsForClass(GymClass $gymClass): void
    {
        if (!$gymClass->is_active) {
            // Cancel future scheduled sessions if class is deactivated
            ClassSession::withoutGlobalScope(BranchScope::class)
                ->where('gym_class_id', $gymClass->id)
                ->where('starts_at', '>=', now())
                ->update(['status' => 'cancelled']);
            return;
        }

        $timeString = $gymClass->schedule_time ?: '09:00';
        $timeParts = explode(':', $timeString);
        $hour = (int) ($timeParts[0] ?? 9);
        $minute = (int) ($timeParts[1] ?? 0);

        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $sessionDate = now()->addDays($dayOffset)->setTime($hour, $minute, 0);

            if ($dayOffset === 0 && $sessionDate->isPast()) {
                // If today's slot already passed, schedule one for 1 hour from now for testing convenience
                $sessionDate = now()->addHours(1)->setMinutes($minute)->setSeconds(0);
            }

            $sessionEnd = (clone $sessionDate)->addMinutes((int) ($gymClass->duration_minutes ?: 60));

            $existingSession = ClassSession::withoutGlobalScope(BranchScope::class)
                ->where('gym_class_id', $gymClass->id)
                ->whereDate('starts_at', $sessionDate->toDateString())
                ->first();

            if (!$existingSession) {
                ClassSession::withoutGlobalScope(BranchScope::class)->create([
                    'branch_id'         => $gymClass->branch_id ?? 1,
                    'gym_class_id'      => $gymClass->id,
                    'trainer_id'        => $gymClass->trainer_id,
                    'starts_at'         => $sessionDate,
                    'ends_at'           => $sessionEnd,
                    'capacity_override' => $gymClass->capacity,
                    'status'            => 'scheduled',
                ]);
            } else {
                $existingSession->update([
                    'trainer_id'        => $gymClass->trainer_id,
                    'capacity_override' => $gymClass->capacity,
                    'status'            => 'scheduled',
                ]);
            }
        }
    }

    public function index(BranchContext $branchContext): Response
    {
        $branch = $branchContext->branch();

        $classesQuery = GymClass::withoutGlobalScope(BranchScope::class)
            ->with(['sessions' => function ($q) {
                $q->where('starts_at', '>=', now());
            }, 'trainer'])
            ->latest();

        $trainersQuery = Trainer::withoutGlobalScope(BranchScope::class)->where('is_active', true);

        if ($branch && isset($branch->id)) {
            $classesQuery->where('branch_id', $branch->id);
            $trainersQuery->where('branch_id', $branch->id);
        }

        return Inertia::render('Classes/Index', [
            'classes' => $classesQuery->get()->map(fn ($c) => [
                'id'               => $c->id,
                'name'             => $c->name,
                'description'      => $c->description,
                'trainer_id'       => $c->trainer_id,
                'trainer'          => $c->trainer ? ($c->trainer->first_name . ' ' . $c->trainer->last_name) : null,
                'capacity'         => $c->capacity,
                'duration_minutes' => $c->duration_minutes,
                'schedule_time'    => $c->schedule_time ?? '09:00',
                'is_active'        => (bool) $c->is_active,
                'upcoming_sessions_count' => $c->sessions->count(),
            ]),
            'trainers' => $trainersQuery->get()->map(fn ($t) => [
                'id'         => $t->id,
                'first_name' => $t->first_name,
                'last_name'  => $t->last_name,
                'name'       => $t->first_name . ' ' . $t->last_name,
            ]),
        ]);
    }

    public function store(Request $request, BranchContext $branchContext): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'trainer_id'       => 'nullable|integer|exists:trainers,id',
            'capacity'         => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'schedule_time'    => 'nullable|string|max:10',
            'is_active'        => 'boolean',
        ]);

        $validated['branch_id'] = $branchContext->branch()?->id ?? 1;
        $validated['schedule_time'] = $validated['schedule_time'] ?? '09:00';

        $gymClass = GymClass::withoutGlobalScope(BranchScope::class)->create($validated);

        self::syncUpcomingSessionsForClass($gymClass);

        return redirect()->back()->with('success', 'Class created and scheduled for member portal.');
    }

    public function update(Request $request, int $gymClass): RedirectResponse
    {
        $gymClassModel = GymClass::withoutGlobalScope(BranchScope::class)->findOrFail($gymClass);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'trainer_id'       => 'nullable|integer|exists:trainers,id',
            'capacity'         => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'schedule_time'    => 'nullable|string|max:10',
            'is_active'        => 'boolean',
        ]);

        $gymClassModel->update($validated);

        self::syncUpcomingSessionsForClass($gymClassModel);

        return redirect()->back()->with('success', 'Class updated and synchronized.');
    }

    public function destroy(int $gymClass): RedirectResponse
    {
        $gymClassModel = GymClass::withoutGlobalScope(BranchScope::class)->findOrFail($gymClass);

        // Delete future bookings and sessions
        $sessionIds = ClassSession::withoutGlobalScope(BranchScope::class)
            ->where('gym_class_id', $gymClassModel->id)
            ->pluck('id');

        ClassBooking::withoutGlobalScope(BranchScope::class)->whereIn('class_session_id', $sessionIds)->delete();
        ClassSession::withoutGlobalScope(BranchScope::class)->whereIn('id', $sessionIds)->delete();

        $gymClassModel->delete();

        return redirect()->back()->with('success', 'Class deleted successfully.');
    }
}
