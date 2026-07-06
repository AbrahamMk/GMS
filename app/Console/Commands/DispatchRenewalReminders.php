<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\Branch;
use App\Models\ReminderLog;
use App\Support\BranchContext;
use Illuminate\Console\Command;

final class DispatchRenewalReminders extends Command
{
    protected $signature = 'memberships:remind-renewals';

    protected $description = 'Create renewal reminder records for memberships approaching expiry.';

    public function handle(BranchContext $branchContext): int
    {
        $thresholds = [7, 3, 1];
        $created = 0;

        Branch::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->chunkById(100, function ($branches) use (&$created, $branchContext, $now, $thresholds): void {
                foreach ($branches as $branch) {
                    $branchContext->setBranch($branch);
                    $now = $branchContext->now();

                    foreach ($thresholds as $days) {
                        $scheduledFor = $now->addDays($days);
                        $reminderWindowStart = $scheduledFor->startOfDay();
                        $reminderWindowEnd = $scheduledFor->endOfDay();

                        Membership::query()
                            ->with('member')
                            ->active()
                            ->whereNotNull('ends_at')
                            ->whereBetween('ends_at', [$reminderWindowStart, $reminderWindowEnd])
                            ->chunkById(100, function ($memberships) use (&$created, $scheduledFor, $days): void {
                                foreach ($memberships as $membership) {
                                    $alreadyQueued = ReminderLog::query()
                                        ->where('type', 'renewal_due')
                                        ->where('membership_id', $membership->getKey())
                                        ->whereDate('scheduled_for', $scheduledFor->toDateString())
                                        ->exists();

                                    if ($alreadyQueued) {
                                        continue;
                                    }

                                    ReminderLog::query()->create([
                                        'member_id' => $membership->member_id,
                                        'membership_id' => $membership->getKey(),
                                        'channel' => 'in_app',
                                        'type' => 'renewal_due',
                                        'scheduled_for' => $scheduledFor,
                                        'status' => 'pending',
                                        'payload' => [
                                            'days_until_expiry' => $days,
                                            'membership_status' => $membership->status,
                                        ],
                                    ]);

                                    $created++;
                                }
                            });
                    }
                }
            });

        $branchContext->clear();

        $this->info(sprintf('Queued %d renewal reminder record(s).', $created));

        return self::SUCCESS;
    }
}
