<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizacionResource extends JsonResource
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
            'phone_numbers' => $this->phone_numbers,
            'building' => $this->whenLoaded('building', fn() => new BuildingResource($this->building)),
            'activities' => $this->whenLoaded('activities', fn() => ActivityResource::collection($this->activities)),
            'distance' => $this->when($this->distance !== null,
                fn() => round((float) $this->distance, 2)
            )

        ];
    }
}
