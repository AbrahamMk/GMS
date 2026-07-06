<?php

declare(strict_types=1);

namespace App\Http\Requests\EquipmentItems;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateEquipmentItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;
        $equipmentItem = $this->route('equipment_item');

        return [
            'asset_tag' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('equipment_items', 'asset_tag')
                    ->where('branch_id', $branchId)
                    ->ignore($equipmentItem),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'equipment_category_id' => ['nullable', 'integer', Rule::exists('equipment_categories', 'id')],
            'model' => ['nullable', 'string', 'max:255'],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipment_items', 'barcode')
                    ->where('branch_id', $branchId)
                    ->ignore($equipmentItem),
            ],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date'],
            'condition' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['available', 'in_use', 'under_repair', 'retired'])],
        ];
    }
}
