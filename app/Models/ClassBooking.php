<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassBooking extends BranchModel
{
    protected function casts(): array
    {
        return [
            'booked_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'waitlist_position' => 'integer',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
