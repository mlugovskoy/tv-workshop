<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepairResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => $this->status,
            'status_history' => $this->whenLoaded(
                'statusHistories',
                fn() => $this->statusHistories
                    ->sortByDesc('created_at')
                    ->values()
                    ->map(fn($history) => [
                        'id' => $history->id,
                        'status' => $history->status,
                        'created_at' => $history->created_at,
                    ])
            ),

            'problem_description' => $this->problem_description,
            'diagnosis' => $this->diagnosis,
            'repair_description' => $this->repair_description,

            'estimated_price' => $this->estimated_price,
            'final_price' => $this->final_price,

            'received_at' => $this->received_at,
            'completed_at' => $this->completed_at,
            'issued_at' => $this->issued_at,

            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'phone' => $this->client->phone
            ],
            'device' => [
                'id' => $this->device->id,
                'brand' => $this->device->brand,
                'model' => $this->device->model,
                'serial_number' => $this->device->serial_number,
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
