<?php

declare(strict_types=1);

namespace App\Models;

class MaintenanceLog extends BranchModel
{
    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'cost' => 'decimal:2',
            'next_due_at' => 'datetime',
        ];
    }
}
