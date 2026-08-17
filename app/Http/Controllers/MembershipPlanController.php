<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Scopes\BranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MembershipPlanController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $member = $user?->member()->first();

        return Inertia::render('Memberships/Index', [
            'plans' => MembershipPlan::withoutGlobalScope(BranchScope::class)
                ->latest()
                ->get()
                ->map(fn (MembershipPlan $plan): array => [
                    'id'            => $plan->id,
                    'code'          => $plan->code,
                    'name'          => $plan->name,
                    'description'   => $plan->description,
                    'type'          => $plan->type,
                    'price'         => $plan->price,
                    'currency'      => $plan->currency ?? 'USD',
                    'duration_days' => $plan->duration_days,
                    'visit_limit'   => $plan->visit_limit,
                    'is_active'     => (bool) $plan->is_active,
                ])
                ->values(),
            'activeMemberships' => Membership::withoutGlobalScope(BranchScope::class)
                ->with(['member', 'plan'])
                ->when($user?->hasRole('member') === true, static function ($query) use ($member): void {
                    if ($member !== null) {
                        $query->where('member_id', $member->getKey());
                    }
                })
                ->latest('starts_at')
                ->limit(15)
                ->get()
                ->map(fn (Membership $membership): array => [
                    'id'               => $membership->id,
                    'status'           => $membership->status,
                    'remaining_visits' => $membership->remaining_visits,
                    'starts_at'        => $membership->starts_at,
                    'ends_at'          => $membership->ends_at,
                    'member'           => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                    'plan'             => $membership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
                ])
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:recurring,one-time,fixed',
            'price'         => 'required|numeric|min:0',
            'currency'      => 'nullable|string|max:10',
            'duration_days' => 'nullable|integer|min:1',
            'visit_limit'   => 'nullable|integer|min:1',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['branch_id'] = $request->user()?->branch_id ?? 1;
        $validated['code'] = $validated['code'] ?: 'PLAN-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 4)) . '-' . rand(100, 999);
        $validated['currency'] = $validated['currency'] ?? 'USD';
        $validated['is_active'] = $validated['is_active'] ?? true;

        MembershipPlan::withoutGlobalScope(BranchScope::class)->create($validated);

        return redirect()->back()->with('success', 'Membership plan created successfully.');
    }

    public function update(Request $request, int $membershipPlan): RedirectResponse
    {
        $plan = MembershipPlan::withoutGlobalScope(BranchScope::class)->findOrFail($membershipPlan);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:recurring,one-time,fixed',
            'price'         => 'required|numeric|min:0',
            'currency'      => 'nullable|string|max:10',
            'duration_days' => 'nullable|integer|min:1',
            'visit_limit'   => 'nullable|integer|min:1',
            'is_active'     => 'nullable|boolean',
        ]);

        $plan->update($validated);

        return redirect()->back()->with('success', 'Membership plan updated successfully.');
    }

    public function destroy(int $membershipPlan): RedirectResponse
    {
        $plan = MembershipPlan::withoutGlobalScope(BranchScope::class)->findOrFail($membershipPlan);
        $plan->delete();

        return redirect()->back()->with('success', 'Membership plan deleted.');
    }
}
