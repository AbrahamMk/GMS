<?php

declare(strict_types=1);

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'member.first_name' => ['nullable', 'string', 'max:255'],
            'member.last_name' => ['nullable', 'string', 'max:255'],
            'member.gender' => ['nullable', 'string', 'max:20'],
            'member.date_of_birth' => ['nullable', 'date'],
            'member.address' => ['nullable', 'string'],
            'member.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'member.emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'member.photo_path' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
