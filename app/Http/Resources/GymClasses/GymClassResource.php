<?php

declare(strict_types=1);

namespace App\Http\Resources\GymClasses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class GymClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'description' => $this->description,
            'trainer_user_id' => $this->trainer_user_id,
            'capacity' => $this->capacity,
            'duration_minutes' => $this->duration_minutes,
            'is_active' => $this->is_active,
            'trainer' => $this->whenLoaded('trainer', fn () => [
                'id' => $this->trainer->id,
                'name' => $this->trainer->name,
                'email' => $this->trainer->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
