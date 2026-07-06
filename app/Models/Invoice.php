<?php

declare(strict_types=1);

namespace App\Models;

class Invoice extends BranchModel
{
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }
}
