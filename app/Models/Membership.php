<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends BranchModel
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'last_renewed_at' => 'datetime',
            'auto_renew_enabled' => 'boolean',
            'activated_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function freezes(): HasMany
    {
        return $this->hasMany(MembershipFreeze::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isExpired(): bool
    {
        if ($this->status !== 'active') {
            return true;
        }

        if ($this->plan?->isTimeBased() && $this->ends_at !== null && $this->ends_at->isPast()) {
            return true;
        }

        if ($this->plan?->isPackBased() && $this->remaining_visits !== null && $this->remaining_visits <= 0) {
            return true;
        }

        return false;
    }
}
