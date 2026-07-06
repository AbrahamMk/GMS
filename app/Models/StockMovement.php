<?php

declare(strict_types=1);

namespace App\Models;

class StockMovement extends BranchModel
{
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }
}
