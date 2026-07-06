<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MembershipPlans\StoreMembershipPlanRequest;
use App\Http\Requests\MembershipPlans\UpdateMembershipPlanRequest;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MembershipPlanController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->get('search');

        return Inertia::render('MembershipPlans/Index', [
            'plans' => MembershipPlan::query()
                ->when($search, fn ($query, $search) => $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                }))
                ->latest()
                ->paginate(15),
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function store(StoreMembershipPlanRequest $request)
    {
        $plan = MembershipPlan::query()->create($request->validated());

        return redirect()->route('portal.membership-plans')->with('success', 'Membership plan created successfully.');
    }

    public function show(MembershipPlan $membershipPlan): Response
    {
        return Inertia::render('MembershipPlans/Show', [
            'plan' => $membershipPlan,
        ]);
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membershipPlan)
    {
        $membershipPlan->fill($request->validated())->save();

        return redirect()->route('portal.membership-plans')->with('success', 'Membership plan updated successfully.');
    }

    public function destroy(MembershipPlan $membershipPlan)
    {
        $membershipPlan->delete();

        return redirect()->route('portal.membership-plans')->with('success', 'Membership plan deleted successfully.');
    }
}
