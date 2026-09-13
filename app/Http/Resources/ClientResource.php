<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'name' => $this->name,
            'name_search' => $this->name_search,
            'phone' => $this->phone,
            'comment' => $this->comment,
            'created_at' => $this->createdAtFormatted,
            'updated_at' => $this->updatedAtFormatted
        ];
    }
}
