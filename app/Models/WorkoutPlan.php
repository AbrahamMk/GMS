<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutPlan extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'category',
        'difficulty',
        'duration_minutes',
        'calories_est',
        'target_muscle',
        'created_by_user_id',
    ];

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutPlanExercise::class)->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(MemberWorkoutAssignment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
