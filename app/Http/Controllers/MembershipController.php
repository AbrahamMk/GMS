<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Memberships\ActivateMembership;
use App\Actions\Memberships\RenewMembership;
use App\Http\Requests\Memberships\ActivateMembershipRequest;
use App\Http\Requests\Memberships\RenewMembershipRequest;
use App\Http\Resources\Memberships\MembershipResource;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use Carbon\CarbonImmutable;

final class MembershipController extends Controller
{
    public function activate(ActivateMembershipRequest $request, ActivateMembership $action): MembershipResource
    {
        $payload = $request->validated();
        $member = Member::query()->findOrFail((int) $payload['member_id']);
        $plan = MembershipPlan::query()->findOrFail((int) $payload['membership_plan_id']);

        $membership = $action->handle(
            $member,
            $plan,
            $request->user(),
            isset($payload['starts_at']) ? CarbonImmutable::parse($payload['starts_at']) : null,
            (bool) ($payload['auto_renew_enabled'] ?? false),
            $payload['renewal_source'] ?? 'manual',
            $payload['notes'] ?? null,
        );

        return new MembershipResource($membership->load('plan'));
    }

    public function renew(RenewMembershipRequest $request, RenewMembership $action): MembershipResource
    {
        $payload = $request->validated();
        $membership = Membership::query()->findOrFail((int) $payload['membership_id']);

        $membership = $action->handle(
            $membership,
            $request->user(),
            now(),
            (bool) ($payload['auto_renew'] ?? false),
            $payload['renewal_source'] ?? 'manual',
            $payload['metadata'] ?? [],
        );

        return new MembershipResource($membership->load('plan'));
    }
}
