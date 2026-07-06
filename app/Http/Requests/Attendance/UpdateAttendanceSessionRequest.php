<?php

declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAttendanceSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:open,closed,rejected'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,rejected'],
            'checked_out_at' => ['nullable', 'date'],
        ];
    }
}
