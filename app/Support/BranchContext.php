<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\BranchContextNotResolvedException;
use App\Models\Branch;
use Carbon\CarbonImmutable;

final class BranchContext
{
    private ?int $branchId = null;

    private ?Branch $branch = null;

    public function setBranchId(?int $branchId): void
    {
        $this->branchId = $branchId;
        $this->branch = null;
    }

    public function setBranch(?Branch $branch): void
    {
        $this->branch = $branch;
        $this->branchId = $branch?->getKey();
    }

    public function branchId(): ?int
    {
        return $this->branchId;
    }

    public function requireBranchId(): int
    {
        if ($this->branchId === null) {
            throw new BranchContextNotResolvedException('Branch context is required for branch-scoped model access.');
        }

        return $this->branchId;
    }

    public function branch(): ?Branch
    {
        return $this->branch;
    }

    public function clear(): void
    {
        $this->branchId = null;
        $this->branch = null;
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now($this->branch?->timezone ?? config('app.timezone'));
    }
}
