<?php

declare(strict_types=1);

namespace App\Http\Resources\StockItems;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'unit' => $this->unit,
            'barcode' => $this->barcode,
            'expiry_date' => $this->expiry_date,
            'current_stock' => $this->current_stock,
            'reorder_level' => $this->reorder_level,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
