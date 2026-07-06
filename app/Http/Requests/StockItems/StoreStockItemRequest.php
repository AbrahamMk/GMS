<?php

declare(strict_types=1);

namespace App\Http\Requests\StockItems;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('stock_items', 'sku')->where('branch_id', $branchId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('stock_items', 'barcode')->where('branch_id', $branchId),
            ],
            'expiry_date' => ['nullable', 'date'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
