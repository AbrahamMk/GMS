<?php

declare(strict_types=1);

namespace App\Http\Requests\EquipmentCategories;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateEquipmentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;
        $category = $this->route('equipment_category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('equipment_categories', 'name')
                    ->where('branch_id', $branchId)
                    ->ignore($category),
            ],
        ];
    }
}
