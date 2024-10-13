<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->getKey(),

            // General Information
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'birthdate' => $this->birthdate ? $this->birthdate->format('Y-m-d') : null,

            // Location Information
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state_id' => $this->state_id,
            'zip_code' => $this->zip_code,
            'timezone' => $this->timezone,
        
            // Personal Health Information
            'gender' => $this->gender,
            'height' => $this->height,
            'weight' => $this->weight,
            'roles' => $this->getRoleNames(),
            'medical_specialty' => $this->medicalSpecialty,
            'medical_specialty_id' => $this->medical_specialty_id,
            'user_type' => $this->userType,
            'user_type_id' => $this->user_type_id,

            'organization_ids' => $this->whenLoaded('organizations', $this->organizations->pluck('organization_id'), []),

            // Other
            'can_be_impersonated' => $this->canBeImpersonated(),

            // Other
            'pivot_document_types' => $this->getDocumentTypes()->reduce(function ($carry, $documentType) {
                $carry[$documentType['document_type_id']] = $documentType;
                return $carry;
            }, []),
        ];
    }
}
