<?php

declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'attendance_session_id' => [
                'required',
                'integer',
                Rule::exists('attendance_sessions', 'id')->where('branch_id', $branchId),
            ],
            'check_out_method' => ['nullable', 'in:qr,manual,mobile'],
            'verified_by_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
