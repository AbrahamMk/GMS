<?php

declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CheckInRequest extends FormRequest
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
            'qr_token' => ['nullable', 'string', 'max:255'],
            'check_in_method' => ['nullable', 'in:qr,manual,mobile'],
            'device_info' => ['nullable', 'array'],
            'device_info.user_agent' => ['nullable', 'string'],
            'verified_by_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
