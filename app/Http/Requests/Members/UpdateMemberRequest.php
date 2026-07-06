<?php

declare(strict_types=1);

namespace App\Http\Requests\Members;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;
        $member = $this->route('member');

        return [
            'member_code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('members', 'member_code')
                    ->where('branch_id', $branchId)
                    ->ignore($member),
            ],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'photo_path' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'joined_at' => ['nullable', 'date'],
        ];
    }
}
