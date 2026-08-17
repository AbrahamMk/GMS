<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberWorkoutAssignment extends Model
{
    protected $fillable = [
        'member_id',
        'workout_plan_id',
        'assigned_at',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}
