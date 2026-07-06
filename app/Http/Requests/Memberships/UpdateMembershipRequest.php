<?php

declare(strict_types=1);

namespace App\Http\Requests\Memberships;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:pending,active,expired,paused,cancelled'],
            'remaining_visits' => ['nullable', 'integer', 'min:0'],
            'total_visits' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'auto_renew_enabled' => ['nullable', 'boolean'],
        ];
    }
}
