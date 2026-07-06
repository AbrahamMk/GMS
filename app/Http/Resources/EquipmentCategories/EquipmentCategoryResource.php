<?php

declare(strict_types=1);

namespace App\Http\Resources\EquipmentCategories;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EquipmentCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
        ];
    }
}
