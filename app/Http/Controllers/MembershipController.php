<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Memberships\ActivateMembership;
use App\Actions\Memberships\RenewMembership;
use App\Http\Requests\Memberships\ActivateMembershipRequest;
use App\Http\Requests\Memberships\RenewMembershipRequest;
use App\Http\Requests\Memberships\UpdateMembershipRequest;
use App\Http\Resources\Memberships\MembershipResource;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MembershipController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->get('search');

        $memberships = Membership::query()
            ->with(['member:id,member_code,first_name,last_name', 'plan:id,name,type,price,currency'])
            ->when($search, function ($query, $search): void {
                $query->whereHas('member', function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->through(fn (Membership $membership): array => [
                'id' => $membership->id,
                'status' => $membership->status,
                'remaining_visits' => $membership->remaining_visits,
                'starts_at' => $membership->starts_at?->toDateString(),
                'ends_at' => $membership->ends_at?->toDateString(),
                'member' => $membership->member?->only(['id', 'member_code', 'first_name', 'last_name']),
                'plan' => $membership->plan?->only(['id', 'name']),
            ]);

        return Inertia::render('Memberships/Index', [
            'memberships' => $memberships,
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function show(Membership $membership): Response
    {
        return Inertia::render('Memberships/Show', [
            'membership' => (new MembershipResource(
                $membership->load(['member', 'plan'])
            ))->resolve(),
        ]);
    }

    public function activate(ActivateMembershipRequest $request, ActivateMembership $action)
    {
        $payload = $request->validated();
        $member = Member::query()->findOrFail((int) $payload['member_id']);
        $plan = MembershipPlan::query()->findOrFail((int) $payload['membership_plan_id']);

        $action->handle(
            $member,
            $plan,
            $request->user(),
            isset($payload['starts_at']) ? CarbonImmutable::parse($payload['starts_at']) : null,
            (bool) ($payload['auto_renew_enabled'] ?? false),
            $payload['renewal_source'] ?? 'manual',
            $payload['notes'] ?? null,
        );

        return redirect()->route('portal.memberships')->with('success', 'Membership activated successfully.');
    }

    public function renew(RenewMembershipRequest $request, RenewMembership $action)
    {
        $payload = $request->validated();
        $membership = Membership::query()->findOrFail((int) $payload['membership_id']);

        $action->handle(
            $membership,
            $request->user(),
            now(),
            (bool) ($payload['auto_renew'] ?? false),
            $payload['renewal_source'] ?? 'manual',
            $payload['metadata'] ?? [],
        );

        return redirect()->route('portal.memberships')->with('success', 'Membership renewed successfully.');
    }

    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        $membership->update($request->validated());

        return redirect()->route('portal.memberships')->with('success', 'Membership updated successfully.');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('portal.memberships')->with('success', 'Membership deleted successfully.');
    }

    public function pause(Membership $membership)
    {
        $membership->update(['status' => 'paused']);

        return redirect()->route('portal.memberships')->with('success', 'Membership paused successfully.');
    }

    public function cancel(Membership $membership)
    {
        $membership->update(['status' => 'cancelled']);

        return redirect()->route('portal.memberships')->with('success', 'Membership cancelled successfully.');
    }
}
