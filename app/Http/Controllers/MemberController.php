<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Scopes\BranchScope;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index()
    {
        return Inertia::render('Members/Index', [
            'summary' => [
                'members' => Member::withoutGlobalScope(BranchScope::class)->count(),
                'active'  => Member::withoutGlobalScope(BranchScope::class)->active()->count(),
                'memberships' => Membership::withoutGlobalScope(BranchScope::class)->active()->count(),
            ],
            'members' => Member::withoutGlobalScope(BranchScope::class)
                ->latest()
                ->limit(12)
                ->get()
                ->map(fn (Member $member): array => [
                    'id'          => $member->id,
                    'member_code' => $member->member_code,
                    'first_name'  => $member->first_name,
                    'last_name'   => $member->last_name,
                    'phone'       => $member->phone,
                    'email'       => $member->email,
                    'status'      => $member->status,
                ])
                ->values(),
        ]);
    }

    public function show(int $member)
    {
        // Reload the member without branch scope so admins without a branch can view any member
        $member = Member::withoutGlobalScope(BranchScope::class)->findOrFail($member);
        $memberships = $member->memberships()
            ->withoutGlobalScope(BranchScope::class)
            ->with('plan')
            ->latest('starts_at')
            ->get();

        $attendances = $member->attendanceSessions()
            ->withoutGlobalScope(BranchScope::class)
            ->latest('checked_in_at')
            ->limit(10)
            ->get()
            ->map(fn ($attendance) => [
                'id'             => $attendance->id,
                'checked_in_at'  => $attendance->checked_in_at,
                'checked_out_at' => $attendance->checked_out_at,
                'status'         => $attendance->status,
            ]);

        return Inertia::render('Members/Show', [
            'member' => [
                'id'            => $member->id,
                'member_code'   => $member->member_code,
                'first_name'    => $member->first_name,
                'last_name'     => $member->last_name,
                'phone'         => $member->phone,
                'email'         => $member->email,
                'status'        => $member->status,
                'date_of_birth' => $member->date_of_birth,
                'gender'        => $member->gender,
                'address'       => $member->address,
                'created_at'    => $member->created_at,
            ],
            'memberships' => $memberships->map(fn ($membership) => [
                'id'               => $membership->id,
                'plan_name'        => $membership->plan?->name ?? 'N/A',
                'status'           => $membership->status,
                'starts_at'        => $membership->starts_at,
                'ends_at'          => $membership->ends_at,
                'remaining_visits' => $membership->remaining_visits,
            ]),
            'attendances' => $attendances,
        ]);
    }
}
