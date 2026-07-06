<?php

declare(strict_types=1);

namespace App\Http\Requests\Portal;

use App\Support\BranchContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CancelBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branchId = app(BranchContext::class)->branchId() ?? 0;

        return [
            'class_booking_id' => [
                'required',
                'integer',
                Rule::exists('class_bookings', 'id')->where('branch_id', $branchId),
            ],
        ];
    }
}
