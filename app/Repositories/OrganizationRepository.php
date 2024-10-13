<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\OrganizationType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use DB;

class OrganizationRepository
{
    /**
     * Get the query for getting all the items.
     */
    public function getItemsQuery(Request $request = null): Builder
    {
        $user = auth()->user();

        return Organization::query()
            ->with([
                'organizationType',
                'state',
            ])

            /**
             * Filter by the search query, if available.
             */
            ->when(!blank($request) && $request->filled('searchQuery'), function ($query) use ($request) {
                $searchTerm = strtolower($request->get('searchQuery'));
                $query
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
            })

            /**
             * Filter by permission, if available.
             */
            ->when(!$user->can('organization.list'), function ($query) use ($user) {
                $query->whereIn('organizations.organization_id', $user->organizations->pluck('organization_id')->toArray());
            });
    }

    /**
     * Create a new instance of the resource.
     */
    public function store(FormRequest $request): Organization
    {
        DB::beginTransaction();

        try {
            $payload = $request->safe()->only([
                'name',
                'organization_type_id',
                'email',
                'phone_number',
                'address_line_1',
                'address_line_2',
                'city',
                'state_id',
                'zip_code',
            ]);

            $organization = Organization::create(array_merge($payload, [
                'slug' => Str::slug($payload['name']),
            ]));

            DB::commit();
            return $organization->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an instance of the resource.
     */
    public function update(FormRequest $request, Organization $organization): Organization
    {
        DB::beginTransaction();

        try {
            $payload = $request->safe()->only([
                'name',
                'organization_type_id',
                'email',
                'phone_number',
                'address_line_1',
                'address_line_2',
                'city',
                'state_id',
                'zip_code',
            ]);

            $organization->update(array_merge($payload, [
                'slug' => Str::slug($payload['name']),
            ]));

            DB::commit();
            return $organization->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an instance of the resource.
     */
    public function destroy(DestroyOrganizationRequest $request, Organization $organization): boolean
    {
        return $organization->delete();
    }
}
