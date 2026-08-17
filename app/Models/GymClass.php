<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymClass extends BranchModel
{
    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'trainer_id',
        'trainer_user_id',
        'capacity',
        'duration_minutes',
        'schedule_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function trainerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }
}
