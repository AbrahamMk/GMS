<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
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
                ->limit(50)
                ->get()
                ->map(fn (Member $member): array => [
                    'id'            => $member->id,
                    'member_code'   => $member->member_code,
                    'first_name'    => $member->first_name,
                    'last_name'     => $member->last_name,
                    'phone'         => $member->phone,
                    'email'         => $member->email,
                    'gender'        => $member->gender,
                    'date_of_birth' => $member->date_of_birth?->format('Y-m-d'),
                    'address'       => $member->address,
                    'status'        => $member->status,
                    'registration_type' => $member->registration_type ?? 'walkin',
                ])
                ->values(),
        ]);
    }

    public function store(Request $request, \App\Support\BranchContext $branchContext)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:255',
            'gender'        => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'address'       => 'nullable|string',
            'status'        => 'required|in:active,inactive,suspended',
        ]);

        $branchId = $branchContext->branch()?->id ?? 1;
        $maxId = Member::withoutGlobalScope(BranchScope::class)->max('id') ?? 0;
        $memberCode = 'MBR-' . str_pad((string)($maxId + 1), 3, '0', STR_PAD_LEFT);

        $validated['branch_id']         = $branchId;
        $validated['member_code']       = $memberCode;
        $validated['registration_type'] = 'walkin';
        $validated['joined_at']         = now();

        Member::withoutGlobalScope(BranchScope::class)->create($validated);

        return redirect()->back()->with('success', 'Member registered successfully.');
    }

    public function update(Request $request, int $member)
    {
        $memberModel = Member::withoutGlobalScope(BranchScope::class)->findOrFail($member);

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:255',
            'gender'        => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'address'       => 'nullable|string',
            'status'        => 'required|in:active,inactive,suspended',
        ]);

        $memberModel->update($validated);

        return redirect()->back()->with('success', 'Member details updated successfully.');
    }

    public function destroy(int $member)
    {
        $memberModel = Member::withoutGlobalScope(BranchScope::class)->findOrFail($member);
        $memberModel->delete();

        return redirect()->back()->with('success', 'Member deleted successfully.');
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

        $plans = MembershipPlan::withoutGlobalScope(BranchScope::class)->get();

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
            'plans' => $plans,
            'attendances' => $attendances,
        ]);
    }

    public function renewMembership(Request $request, int $member, \App\Support\BranchContext $branchContext)
    {
        $memberModel = Member::withoutGlobalScope(BranchScope::class)->findOrFail($member);
        $validated = $request->validate([
            'membership_plan_id' => 'required|integer',
            'duration_months'    => 'nullable|integer|min:1|max:24',
        ]);

        $plan = MembershipPlan::withoutGlobalScope(BranchScope::class)->find($validated['membership_plan_id']);
        $durationMonths = (int) ($validated['duration_months'] ?? 1);

        $startsAt = now();
        $endsAt = now()->addMonths($durationMonths);

        // Deactivate existing active memberships
        \App\Models\Membership::withoutGlobalScope(BranchScope::class)
            ->where('member_id', $memberModel->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Create new active membership
        \App\Models\Membership::withoutGlobalScope(BranchScope::class)->create([
            'branch_id'          => $memberModel->branch_id ?? ($branchContext->branch()?->id ?? 1),
            'member_id'          => $memberModel->id,
            'membership_plan_id' => $plan?->id ?? 1,
            'status'             => 'active',
            'starts_at'          => $startsAt,
            'ends_at'            => $endsAt,
            'remaining_visits'   => 999,
            'auto_renew'         => false,
        ]);

        $memberModel->update(['status' => 'active']);

        // Record Payment entry
        \App\Models\Payment::withoutGlobalScope(BranchScope::class)->create([
            'branch_id'         => $memberModel->branch_id ?? 1,
            'member_id'         => $memberModel->id,
            'invoice_id'        => null,
            'payment_reference' => 'REN-' . time() . '-' . rand(100, 999),
            'method'            => 'card',
            'status'            => 'completed',
            'amount'            => ($plan?->price ?? 50) * $durationMonths,
            'paid_at'           => now(),
            'metadata'          => [
                'title'    => 'Membership Renewal (' . ($plan?->name ?? 'Standard Plan') . ')',
                'currency' => $plan?->currency ?? 'ETB',
            ],
        ]);

        return redirect()->back()->with('success', 'Membership renewed successfully!');
    }
}
