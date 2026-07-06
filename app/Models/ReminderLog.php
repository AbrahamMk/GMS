<?php

declare(strict_types=1);

namespace App\Models;

class ReminderLog extends BranchModel
{
    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'sent_at' => 'datetime',
            'payload' => 'array',
        ];
    }
}
