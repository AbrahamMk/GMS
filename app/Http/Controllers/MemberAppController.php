<?php

namespace App\Http\Controllers;

use App\Models\ClassBooking;
use App\Models\ClassSession;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\MemberWorkoutAssignment;
use App\Models\Membership;
use App\Scopes\BranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberAppController extends Controller
{
    /**
     * Find or create a Member for the authenticated user.
     * Walk-in deduplication: if an existing walk-in member shares the same
     * email or phone, link it to this user instead of creating a duplicate.
     */
    private function getOrCreateMember($user): ?Member
    {
        if (! $user) {
            return null;
        }

        // 1. Already linked by user_id
        $member = Member::withoutGlobalScope(BranchScope::class)
            ->where('user_id', $user->id)
            ->first();

        if ($member) {
            return $member;
        }

        // 2. Walk-in member with same email → link it
        if ($user->email) {
            $member = Member::withoutGlobalScope(BranchScope::class)
                ->whereNull('user_id')
                ->where('email', $user->email)
                ->first();

            if ($member) {
                $member->update([
                    'user_id'           => $user->id,
                    'registration_type' => 'online',
                ]);
                return $member;
            }
        }

        // 3. Walk-in member with same phone → link it
        if ($user->phone ?? null) {
            $member = Member::withoutGlobalScope(BranchScope::class)
                ->whereNull('user_id')
                ->where('phone', $user->phone)
                ->first();

            if ($member) {
                $member->update([
                    'user_id'           => $user->id,
                    'registration_type' => 'online',
                ]);
                return $member;
            }
        }

        // 4. Create new online member
        $nameParts  = explode(' ', $user->name, 2);
        $firstName  = $nameParts[0] ?? 'Member';
        $lastName   = $nameParts[1] ?? 'User';

        $maxId      = Member::withoutGlobalScope(BranchScope::class)->max('id') ?? 0;
        $memberCode = 'MBR-' . str_pad((string) ($maxId + 1), 3, '0', STR_PAD_LEFT);

        $member = Member::withoutGlobalScope(BranchScope::class)->create([
            'user_id'           => $user->id,
            'branch_id'         => $user->branch_id ?? 1,
            'member_code'       => $memberCode,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'email'             => $user->email,
            'phone'             => $user->phone ?? null,
            'status'            => 'active',
            'registration_type' => 'online',
            'joined_at'         => now(),
        ]);

        // Auto-assign a default membership plan if available
        $plan = \App\Models\MembershipPlan::withoutGlobalScope(BranchScope::class)->first();
        if ($plan) {
            Membership::withoutGlobalScope(BranchScope::class)->create([
                'branch_id'          => 1,
                'member_id'          => $member->id,
                'membership_plan_id' => $plan->id,
                'status'             => 'active',
                'starts_at'          => now(),
                'ends_at'            => now()->addMonths(1),
                'remaining_visits'   => 999,
            ]);
        }

        return $member;
    }

    private function ensureUpcomingSessions(): void
    {
        $activeClasses = GymClass::withoutGlobalScope(BranchScope::class)->where('is_active', true)->get();
        foreach ($activeClasses as $gymClass) {
            AdminClassController::syncUpcomingSessionsForClass($gymClass);
        }
    }

    public function dashboard(Request $request): Response
    {
        $user   = $request->user();
        $member = $this->getOrCreateMember($user);

        $this->ensureUpcomingSessions();

        $activeMembership = $member
            ? Membership::withoutGlobalScope(BranchScope::class)
                ->where('member_id', $member->id)
                ->with('plan')
                ->latest('starts_at')
                ->first()
            : null;

        $upcomingClasses = ClassSession::withoutGlobalScope(BranchScope::class)
            ->whereHas('gymClass', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['gymClass', 'trainer', 'bookings'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(5)
            ->get()
            ->map(fn ($session) => [
                'id'        => $session->id,
                'title'     => $session->gymClass?->name ?? 'Gym Session',
                'trainer'   => $session->trainer
                    ? ($session->trainer->first_name . ' ' . $session->trainer->last_name)
                    : ($session->gymClass?->trainer ? ($session->gymClass->trainer->first_name . ' ' . $session->gymClass->trainer->last_name) : 'Staff Coach'),
                'starts_at' => $session->starts_at,
                'capacity'  => $session->capacity_override ?? $session->gymClass?->capacity ?? 20,
                'booked'    => $session->bookings->where('status', 'booked')->count(),
                'is_booked' => $member
                    ? $session->bookings->where('member_id', $member->id)->where('status', 'booked')->isNotEmpty()
                    : false,
            ]);

        $myBookingsCount = $member
            ? ClassBooking::withoutGlobalScope(BranchScope::class)
                ->where('member_id', $member->id)
                ->where('status', 'booked')
                ->count()
            : 0;

        $checkInsThisMonth = $member
            ? $member->attendanceSessions()
                ->withoutGlobalScope(BranchScope::class)
                ->whereMonth('checked_in_at', now()->month)
                ->whereYear('checked_in_at', now()->year)
                ->count()
            : 0;

        // Assigned workout plan for this member
        $assignedWorkout = null;
        if ($member) {
            $assignment = MemberWorkoutAssignment::where('member_id', $member->id)
                ->where('is_active', true)
                ->with(['workoutPlan.exercises'])
                ->latest()
                ->first();

            if ($assignment?->workoutPlan) {
                $assignedWorkout = [
                    'id'               => $assignment->workoutPlan->id,
                    'title'            => $assignment->workoutPlan->name,
                    'category'         => $assignment->workoutPlan->category,
                    'difficulty'       => $assignment->workoutPlan->difficulty,
                    'duration_minutes' => $assignment->workoutPlan->duration_minutes,
                    'calories_est'     => $assignment->workoutPlan->calories_est,
                    'target_muscle'    => $assignment->workoutPlan->target_muscle,
                    'exercise_count'   => $assignment->workoutPlan->exercises->count(),
                ];
            }
        }

        // Days remaining in membership
        $daysRemaining  = null;
        $daysTotal      = null;
        if ($activeMembership?->ends_at && $activeMembership->starts_at) {
            $daysRemaining = max(0, (int) now()->diffInDays($activeMembership->ends_at, false));
            $daysTotal     = max(1, (int) $activeMembership->starts_at->diffInDays($activeMembership->ends_at));
        }

        return Inertia::render('MemberApp/Dashboard', [
            'member'         => $member,
            'user'           => $user,
            'activeMembership' => $activeMembership ? [
                'id'               => $activeMembership->id,
                'status'           => $activeMembership->status,
                'starts_at'        => $activeMembership->starts_at,
                'ends_at'          => $activeMembership->ends_at,
                'remaining_visits' => $activeMembership->remaining_visits,
                'plan_name'        => $activeMembership->plan?->name ?? 'Standard Membership',
                'days_remaining'   => $daysRemaining,
                'days_total'       => $daysTotal,
            ] : null,
            'myBookingsCount'   => $myBookingsCount,
            'checkInsThisMonth' => $checkInsThisMonth,
            'assignedWorkout'   => $assignedWorkout,
            'upcomingClasses'   => $upcomingClasses,
        ]);
    }

    public function classes(Request $request): Response
    {
        $user   = $request->user();
        $member = $this->getOrCreateMember($user);

        $this->ensureUpcomingSessions();

        $classes = ClassSession::withoutGlobalScope(BranchScope::class)
            ->whereHas('gymClass', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['gymClass', 'trainer', 'bookings'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($session) => [
                'id'          => $session->id,
                'gym_class_id'=> $session->gym_class_id,
                'title'       => $session->gymClass?->name ?? 'Fitness Class',
                'description' => $session->gymClass?->description,
                'trainer'     => $session->trainer
                    ? ($session->trainer->first_name . ' ' . $session->trainer->last_name)
                    : ($session->gymClass?->trainer ? ($session->gymClass->trainer->first_name . ' ' . $session->gymClass->trainer->last_name) : 'Staff Coach'),
                'trainer_img' => $session->trainer?->image_url,
                'starts_at'   => $session->starts_at,
                'ends_at'     => $session->ends_at,
                'capacity'    => $session->capacity_override ?? $session->gymClass?->capacity ?? 20,
                'booked'      => $session->bookings->where('status', 'booked')->count(),
                'is_booked'   => $member
                    ? $session->bookings->where('member_id', $member->id)->where('status', 'booked')->isNotEmpty()
                    : false,
            ]);

        return Inertia::render('MemberApp/Classes', [
            'classes' => $classes,
            'member'  => $member,
        ]);
    }

    public function workouts(Request $request): Response
    {
        $user   = $request->user();
        $member = $this->getOrCreateMember($user);

        $workouts = [];

        if ($member) {
            $assignments = MemberWorkoutAssignment::where('member_id', $member->id)
                ->where('is_active', true)
                ->with(['workoutPlan.exercises'])
                ->latest()
                ->get();

            $workouts = $assignments->map(fn ($assignment) => [
                'id'               => $assignment->workoutPlan->id,
                'plan_name'        => $assignment->workoutPlan->name,
                'category'         => $assignment->workoutPlan->category,
                'difficulty'       => $assignment->workoutPlan->difficulty,
                'duration_minutes' => $assignment->workoutPlan->duration_minutes,
                'calories_est'     => $assignment->workoutPlan->calories_est,
                'target_muscle'    => $assignment->workoutPlan->target_muscle,
                'assigned_at'      => $assignment->assigned_at,
                'notes'            => $assignment->notes,
                'exercises'        => $assignment->workoutPlan->exercises->map(fn ($ex) => [
                    'id'          => $ex->id,
                    'name'        => $ex->name,
                    'sets'        => $ex->sets,
                    'reps'        => $ex->reps,
                    'rest_seconds'=> $ex->rest_seconds,
                    'weight_note' => $ex->weight_note,
                    'day_label'   => $ex->day_label,
                ])->values(),
            ])->values();
        }

        return Inertia::render('MemberApp/Workouts', [
            'member'   => $member,
            'workouts' => $workouts,
        ]);
    }

    public function profile(Request $request): Response
    {
        $user   = $request->user();
        $member = $this->getOrCreateMember($user);

        $activeMembership = $member
            ? Membership::withoutGlobalScope(BranchScope::class)
                ->where('member_id', $member->id)
                ->with('plan')
                ->latest('starts_at')
                ->first()
            : null;

        return Inertia::render('MemberApp/Profile', [
            'member' => $member,
            'user'   => $user,
            'activeMembership' => $activeMembership ? [
                'plan_name' => $activeMembership->plan?->name ?? 'Standard Plan',
                'status'    => $activeMembership->status,
                'starts_at' => $activeMembership->starts_at,
                'ends_at'   => $activeMembership->ends_at,
            ] : null,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user   = $request->user();
        $member = $this->getOrCreateMember($user);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:255',
        ]);

        if ($member) {
            $member->update($validated);
        }

        $user->update([
            'name'  => $validated['first_name'] . ' ' . $validated['last_name'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
