<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentTargetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'staff_id' => $this->staff_id, // or agent_id
            'period_month' => $this->period_month,
            'target_type' => $this->target_type,
            'target_qty' => $this->target_qty,
            'reward_per_unit_iqd' => $this->reward_per_unit_iqd,
            'min_achievement_percent' => $this->min_achievement_percent,
            'is_active' => $this->is_active,

            // Relationships
            'items' => $this->whenLoaded('items'),
            'results' => $this->whenLoaded('results'),

            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
