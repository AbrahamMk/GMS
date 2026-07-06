<?php

declare(strict_types=1);

namespace App\Models;

class PaymentAllocation extends BranchModel
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}
