<?php

declare(strict_types=1);

namespace App\Models;

class Payment extends BranchModel
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
