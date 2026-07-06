<?php

declare(strict_types=1);

namespace App\Http\Resources\Memberships;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MembershipPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'duration_days' => $this->duration_days,
            'visit_limit' => $this->visit_limit,
            'price' => $this->price,
            'currency' => $this->currency,
            'grace_period_days' => $this->grace_period_days,
            'allowed_check_in_window_hours' => $this->allowed_check_in_window_hours,
            'max_daily_visits' => $this->max_daily_visits,
            'freeze_allowance_days' => $this->freeze_allowance_days,
            'auto_renewable' => $this->auto_renewable,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
