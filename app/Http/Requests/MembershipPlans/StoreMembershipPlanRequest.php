<?php

declare(strict_types=1);

namespace App\Http\Requests\MembershipPlans;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreMembershipPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('membership_plans', 'code')->where('branch_id', $branchId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:time_based,pack_based,hybrid'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'visit_limit' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'grace_period_days' => ['nullable', 'integer', 'min:0'],
            'allowed_check_in_window_hours' => ['nullable', 'integer', 'min:0'],
            'max_daily_visits' => ['nullable', 'integer', 'min:1'],
            'freeze_allowance_days' => ['nullable', 'integer', 'min:0'],
            'auto_renewable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
