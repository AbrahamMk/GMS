<?php

declare(strict_types=1);

namespace App\Http\Resources\EquipmentItems;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EquipmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'equipment_category_id' => $this->equipment_category_id,
            'asset_tag' => $this->asset_tag,
            'name' => $this->name,
            'model' => $this->model,
            'barcode' => $this->barcode,
            'serial_number' => $this->serial_number,
            'purchase_date' => $this->purchase_date,
            'warranty_expires_at' => $this->warranty_expires_at,
            'condition' => $this->condition,
            'location' => $this->location,
            'status' => $this->status,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
