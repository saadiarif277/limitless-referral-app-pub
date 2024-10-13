<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        return [
            'organization_id' => $this->organization_id,
            'organization_type_id' => $this->organization_type_id,
            'organization_type' => new OrganizationTypeResource($this->whenLoaded('organizationType')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            
            // Contact Information
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            
            // Location Information
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state_id' => $this->state_id,
            'state' => StateResource::collection($this->whenLoaded('state')),
            'zip_code' => $this->zip_code,
        ];
    }
}
