<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Memberships\ExpireMemberships;
use App\Models\Branch;
use App\Support\BranchContext;
use Illuminate\Console\Command;

final class ProcessMembershipExpiry extends Command
{
    protected $signature = 'memberships:expire';

    protected $description = 'Expire memberships that have passed their validity window or exhausted visits.';

    public function handle(ExpireMemberships $expireMemberships, BranchContext $branchContext): int
    {
        $totalExpired = 0;

        Branch::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->chunkById(100, function ($branches) use (&$totalExpired, $expireMemberships, $branchContext): void {
                foreach ($branches as $branch) {
                    $branchContext->setBranch($branch);
                    $totalExpired += $expireMemberships->handle();
                }
            });

        $branchContext->clear();

        $this->info(sprintf('Expired %d membership(s).', $totalExpired));

        return self::SUCCESS;
    }
}
