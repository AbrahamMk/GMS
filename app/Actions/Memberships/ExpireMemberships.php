<?php

declare(strict_types=1);

namespace App\Actions\Memberships;

use App\Models\Membership;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Support\Facades\DB;

final class ExpireMemberships
{
    public function handle(?User $performedBy = null): int
    {
        $expiredCount = 0;
        $now = app(BranchContext::class)->now();

        Membership::query()
            ->with('plan')
            ->active()
            ->where(function ($query) use ($now): void {
                $query->where(function ($nested) use ($now): void {
                    $nested->whereHas('plan', static function ($planQuery): void {
                        $planQuery->whereIn('type', ['time_based', 'hybrid']);
                    })->whereNotNull('ends_at')->where('ends_at', '<', $now);
                })->orWhere(function ($nested): void {
                    $nested->whereHas('plan', static function ($planQuery): void {
                        $planQuery->whereIn('type', ['pack_based', 'hybrid']);
                    })->whereNotNull('remaining_visits')->where('remaining_visits', '<=', 0);
                });
            })
            ->chunkById(100, function ($memberships) use (&$expiredCount, $now, $performedBy): void {
                foreach ($memberships as $membership) {
                    DB::transaction(function () use ($membership, $now, $performedBy, &$expiredCount): void {
                        $previousEndsAt = $membership->ends_at;
                        $previousRemainingVisits = $membership->remaining_visits;

                        $membership->forceFill(['status' => 'expired'])->save();

                        MembershipTransaction::query()->create([
                            'membership_id' => $membership->getKey(),
                            'type' => 'expiry',
                            'previous_ends_at' => $previousEndsAt,
                            'previous_remaining_visits' => $previousRemainingVisits,
                            'new_ends_at' => $now,
                            'new_remaining_visits' => $previousRemainingVisits,
                            'performed_by_user_id' => $performedBy?->getKey(),
                            'metadata' => [
                                'expired_at' => $now->toDateTimeString(),
                            ],
                        ]);

                        $expiredCount++;
                    });
                }
            });

        return $expiredCount;
    }
}
