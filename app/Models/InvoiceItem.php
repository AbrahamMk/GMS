<?php

declare(strict_types=1);

namespace App\Models;

class InvoiceItem extends BranchModel
{
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
