<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MembershipPlans\StoreMembershipPlanRequest;
use App\Http\Requests\MembershipPlans\UpdateMembershipPlanRequest;
use App\Http\Resources\Memberships\MembershipPlanResource;
use App\Models\MembershipPlan;

final class MembershipPlanController extends Controller
{
    public function index()
    {
        return MembershipPlanResource::collection(
            MembershipPlan::query()->latest()->paginate()
        );
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
