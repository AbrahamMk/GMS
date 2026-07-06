<?php

declare(strict_types=1);

namespace App\Actions\Memberships;

use App\Models\Membership;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Support\BranchContext;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RenewMembership
{
    public function handle(
        Membership $membership,
        ?User $performedBy = null,
        ?CarbonInterface $renewedAt = null,
        bool $autoRenew = false,
        ?string $source = 'manual',
        array $metadata = [],
    ): Membership {
        $membership->loadMissing('plan');

        if ($membership->plan === null || ! $membership->plan->is_active) {
            throw ValidationException::withMessages([
                'membership_id' => 'The membership plan is inactive or missing.',
            ]);
        }

        return DB::transaction(function () use ($membership, $performedBy, $renewedAt, $autoRenew, $source, $metadata): Membership {
            $renewedAt = $renewedAt?->toImmutable() ?? app(BranchContext::class)->now();
            $baseDate = $membership->ends_at !== null && $membership->ends_at->isFuture()
                ? $membership->ends_at
                : $renewedAt;

            $newEndsAt = $membership->plan->isTimeBased() && $membership->plan->duration_days !== null
                ? $baseDate->addDays((int) $membership->plan->duration_days)
                : $membership->ends_at;

            $newRemainingVisits = $membership->remaining_visits;

            if ($membership->plan->isPackBased()) {
                $topUp = $membership->plan->visit_limit ?? 0;
                $newRemainingVisits = ($membership->remaining_visits ?? 0) + $topUp;
                $membership->total_visits = ($membership->total_visits ?? 0) + $topUp;
            }

            $previousRemainingVisits = $membership->remaining_visits;
            $previousEndsAt = $membership->ends_at;

            $membership->forceFill([
                'status' => 'active',
                'starts_at' => $membership->starts_at ?? $renewedAt,
                'ends_at' => $newEndsAt,
                'last_renewed_at' => $renewedAt,
                'remaining_visits' => $newRemainingVisits,
                'auto_renew_enabled' => $autoRenew,
                'renewal_source' => $source,
            ])->save();

            MembershipTransaction::query()->create([
                'membership_id' => $membership->getKey(),
                'type' => 'renewal',
                'previous_ends_at' => $previousEndsAt,
                'new_ends_at' => $membership->ends_at,
                'previous_remaining_visits' => $previousRemainingVisits,
                'new_remaining_visits' => $membership->remaining_visits,
                'performed_by_user_id' => $performedBy?->getKey(),
                'metadata' => $metadata + [
                    'auto_renew' => $autoRenew,
                    'renewal_source' => $source,
                ],
            ]);

            return $membership;
        });
    }
}
