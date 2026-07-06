<?php

declare(strict_types=1);

namespace App\Http\Requests\Memberships;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RenewMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'membership_id' => [
                'required',
                'integer',
                Rule::exists('memberships', 'id')->where('branch_id', $branchId),
            ],
            'auto_renew' => ['nullable', 'boolean'],
            'renewal_source' => ['nullable', 'in:manual,auto,promo'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
