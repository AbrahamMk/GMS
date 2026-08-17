<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends BranchModel
{
    protected $fillable = [
        'branch_id',
        'gym_class_id',
        'trainer_id',
        'starts_at',
        'ends_at',
        'capacity_override',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity_override' => 'integer',
        ];
    }

    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class, 'gym_class_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class);
    }
}
