<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Scopes\BranchScope;
use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope());

        static::creating(static function (Model $model): void {
            if ($model->getAttribute('branch_id') !== null) {
                return;
            }

            $model->setAttribute('branch_id', app(BranchContext::class)->requireBranchId());
        });
    }

    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->withoutGlobalScope(BranchScope::class)->where('branch_id', $branchId);
    }
}
