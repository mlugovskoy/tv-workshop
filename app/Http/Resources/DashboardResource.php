<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'statistics' => [
                'in_repair' => $this['in_repair'],
                'ready' => $this['ready'],
                'new_this_month' => $this['new_this_month'],
                'revenue_this_month' => $this['revenue_this_month'],
            ],
            'recent_repairs' => $this['recent_repairs']
                ->map(fn($repair) => [
                    'id' => $repair->id,

                    'client' => [
                        'id' => $repair->client->id,
                        'name' => $repair->client->name,
                    ],

                    'device' => [
                        'id' => $repair->device->id,
                        'brand' => $repair->device->brand,
                        'model' => $repair->device->model,
                    ],

                    'status' => $repair->status,
                    'created_at' => $repair->created_at,
                ]),
        ];
    }
}
