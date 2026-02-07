<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesAgentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'salary' => $this->salary,
            'commission_rate' => $this->commission_rate,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'account_id' => $this->account_id,

            // Stats
            'sales_count' => $this->whenCounted('sales'),

            // Relationships
            'account' => $this->whenLoaded('account'),

            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
