<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
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
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name
            ],
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'comment' => $this->comment,
            'created_at' => $this->createdAtFormatted,
            'updated_at' => $this->updatedAtFormatted
        ];
    }
}
