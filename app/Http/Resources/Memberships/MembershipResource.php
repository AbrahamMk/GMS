<?php

declare(strict_types=1);

namespace App\Http\Resources\Memberships;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'member_id' => $this->member_id,
            'membership_plan_id' => $this->membership_plan_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'last_renewed_at' => $this->last_renewed_at,
            'remaining_visits' => $this->remaining_visits,
            'total_visits' => $this->total_visits,
            'auto_renew_enabled' => $this->auto_renew_enabled,
            'renewal_source' => $this->renewal_source,
            'activated_at' => $this->activated_at,
            'notes' => $this->notes,
            'plan' => $this->relationLoaded('plan') ? new MembershipPlanResource($this->plan) : null,
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
