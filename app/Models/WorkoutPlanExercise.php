<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutPlanExercise extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'name',
        'sets',
        'reps',
        'rest_seconds',
        'weight_note',
        'day_label',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sets' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id');
    }
}
