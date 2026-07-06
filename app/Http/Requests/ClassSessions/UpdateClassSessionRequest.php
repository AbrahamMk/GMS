<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassSessions;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateClassSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'gym_class_id' => ['required', 'integer', 'exists:gym_classes,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'capacity_override' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled'],
        ];
    }
}
