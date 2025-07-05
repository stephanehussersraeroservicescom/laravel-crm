<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'number_of_aircraft' => $this->number_of_aircraft,
            'owner' => $this->owner,
            'comment' => $this->comment,
            'airline_disclosed' => $this->airline_disclosed,
            'airline_code_placeholder' => $this->airline_code_placeholder,
            'confidentiality_notes' => $this->confidentiality_notes,
            'display_airline' => $this->display_airline,
            'display_airline_code' => $this->display_airline_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'airline' => new AirlineResource($this->whenLoaded('airline')),
            'aircraft_type' => new AircraftTypeResource($this->whenLoaded('aircraftType')),
            'design_status' => new StatusResource($this->whenLoaded('designStatus')),
            'commercial_status' => new StatusResource($this->whenLoaded('commercialStatus')),
            'opportunities' => OpportunityResource::collection($this->whenLoaded('opportunities')),
            
            // Counts
            'opportunities_count' => $this->opportunities()->count(),
        ];
    }
}
