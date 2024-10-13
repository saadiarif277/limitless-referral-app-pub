<?php

namespace App\Http\Resources;

use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class OrganizationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'organization_type_id' => $this->organization_type_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'organizations' => OrganizationResource::collection($this->whenLoaded('organizations', $this->getOrganizations($request), [])),
        ];
    }

    /**
     * Get the organizations of the organization type.
     */
    public function getOrganizations(Request $request): Collection
    { 
        $organizationRepository = new OrganizationRepository();

        return $this
            ->organizations()
            ->whereIn('organizations.organization_id', $organizationRepository->getItemsQuery($request)->pluck('organization_id'))
            ->get();
    }
}
