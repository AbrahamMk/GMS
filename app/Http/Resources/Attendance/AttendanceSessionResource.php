<?php

declare(strict_types=1);

namespace App\Http\Resources\Attendance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AttendanceSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'member_id' => $this->member_id,
            'membership_id' => $this->membership_id,
            'qr_token_id' => $this->qr_token_id,
            'checked_in_at' => $this->checked_in_at,
            'checked_out_at' => $this->checked_out_at,
            'check_in_ip' => $this->check_in_ip,
            'device_info' => $this->device_info,
            'verified_by_user_id' => $this->verified_by_user_id,
            'check_in_method' => $this->check_in_method,
            'check_out_method' => $this->check_out_method,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'member' => $this->whenLoaded('member', fn () => [
                'id' => $this->member->id,
                'member_code' => $this->member->member_code,
                'first_name' => $this->member->first_name,
                'last_name' => $this->member->last_name,
                'phone' => $this->member->phone,
                'email' => $this->member->email,
                'status' => $this->member->status,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
