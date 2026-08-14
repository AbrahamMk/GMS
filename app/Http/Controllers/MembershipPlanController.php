<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MembershipPlans\StoreMembershipPlanRequest;
use App\Http\Requests\MembershipPlans\UpdateMembershipPlanRequest;
use App\Http\Resources\Memberships\MembershipPlanResource;
use App\Models\Membership;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $member = $user?->member()->first();

        return Inertia::render('Memberships/Index', [
            'plans' => MembershipPlan::query()
                ->active()
                ->latest()
                ->limit(12)
                ->get()
                ->map(fn (MembershipPlan $plan): array => [
                    'id' => $plan->id,
                    'code' => $plan->code,
                    'name' => $plan->name,
                    'type' => $plan->type,
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'duration_days' => $plan->duration_days,
                    'visit_limit' => $plan->visit_limit,
                ])
                ->values(),
            'activeMemberships' => Membership::query()
                ->with(['member', 'plan'])
                ->when($user?->hasRole('member') === true, static function ($query) use ($member): void {
                    if ($member !== null) {
                        $query->where('member_id', $member->getKey());
                    }
                })
                ->active()
                ->latest('starts_at')
                ->limit(10)
                ->get()
                ->map(fn (Membership $membership): array => [
                    'id' => $membership->id,
                    'status' => $membership->status,
                    'remaining_visits' => $membership->remaining_visits,
                    'starts_at' => $membership->starts_at,
                    'ends_at' => $membership->ends_at,
                    'member' => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                    'plan' => $membership->plan?->only(['id', 'name', 'type', 'price', 'currency']),
                ])
                ->values(),
        ]);
    }

    public function store(StoreMembershipPlanRequest $request): MembershipPlanResource
    {
        $plan = MembershipPlan::query()->create($request->validated());

        return new MembershipPlanResource($plan);
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membershipPlan): MembershipPlanResource
    {
        $membershipPlan->fill($request->validated())->save();

        return new MembershipPlanResource($membershipPlan->refresh());
    }
}
