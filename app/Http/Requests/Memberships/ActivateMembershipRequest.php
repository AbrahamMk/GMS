<?php

declare(strict_types=1);

namespace App\Http\Requests\Memberships;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ActivateMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'member_id' => [
                'required',
                'integer',
                Rule::exists('members', 'id')->where('branch_id', $branchId),
            ],
            'membership_plan_id' => [
                'required',
                'integer',
                Rule::exists('membership_plans', 'id')->where('branch_id', $branchId),
            ],
            'starts_at' => ['nullable', 'date'],
            'auto_renew_enabled' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'renewal_source' => ['nullable', 'in:manual,auto,promo'],
        ];
    }
}
