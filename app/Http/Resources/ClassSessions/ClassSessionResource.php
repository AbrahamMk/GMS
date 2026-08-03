<?php

declare(strict_types=1);

namespace App\Http\Resources\ClassSessions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ClassSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'gym_class_id' => $this->gym_class_id,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'capacity_override' => $this->capacity_override,
            'status' => $this->status,
            'gym_class' => $this->whenLoaded('gymClass', fn () => [
                'id' => $this->gymClass->id,
                'name' => $this->gymClass->name,
                'capacity' => $this->gymClass->capacity,
            ]),
            'bookings_count' => $this->whenLoaded('bookings', fn () => $this->bookings->count()),
            'bookings' => $this->whenLoaded('bookings', fn () => $this->bookings->map(fn ($booking) => [
                'id' => $booking->id,
                'member_id' => $booking->member_id,
                'status' => $booking->status,
                'waitlist_position' => $booking->waitlist_position,
                'created_at' => $booking->created_at,
            ])->values()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
