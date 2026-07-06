<?php

declare(strict_types=1);

namespace App\Actions\Memberships;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Support\BranchContext;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ActivateMembership
{
    public function handle(
        Member $member,
        MembershipPlan $plan,
        ?User $performedBy = null,
        ?CarbonInterface $startsAt = null,
        bool $autoRenewEnabled = false,
        ?string $source = 'manual',
        ?string $notes = null,
    ): Membership {
        if (! $plan->is_active) {
            throw ValidationException::withMessages([
                'membership_plan_id' => 'The selected membership plan is inactive.',
            ]);
        }

        return DB::transaction(function () use ($member, $plan, $performedBy, $startsAt, $autoRenewEnabled, $source, $notes): Membership {
            $startsAt = $startsAt?->toImmutable() ?? app(BranchContext::class)->now();
            $endsAt = $plan->isTimeBased() && $plan->duration_days !== null
                ? $startsAt->addDays((int) $plan->duration_days)
                : null;

            $membership = Membership::query()->create([
                'member_id' => $member->getKey(),
                'membership_plan_id' => $plan->getKey(),
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'last_renewed_at' => $startsAt,
                'remaining_visits' => $plan->isPackBased() ? $plan->visit_limit : null,
                'total_visits' => $plan->isPackBased() ? $plan->visit_limit : null,
                'auto_renew_enabled' => $autoRenewEnabled,
                'renewal_source' => $source,
                'activated_at' => $startsAt,
                'notes' => $notes,
            ]);

            MembershipTransaction::query()->create([
                'membership_id' => $membership->getKey(),
                'type' => 'activation',
                'new_starts_at' => $membership->starts_at,
                'new_ends_at' => $membership->ends_at,
                'new_remaining_visits' => $membership->remaining_visits,
                'performed_by_user_id' => $performedBy?->getKey(),
                'metadata' => [
                    'membership_plan_id' => $plan->getKey(),
                    'auto_renew_enabled' => $autoRenewEnabled,
                ],
            ]);

            return $membership;
        });
    }
}
