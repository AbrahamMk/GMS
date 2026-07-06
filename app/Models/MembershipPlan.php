<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends BranchModel
{
    protected function casts(): array
    {
        return [
            'auto_renewable' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function isPackBased(): bool
    {
        return in_array($this->type, ['pack_based', 'hybrid'], true);
    }

    public function isTimeBased(): bool
    {
        return in_array($this->type, ['time_based', 'hybrid'], true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
