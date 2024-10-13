<?php

namespace App\Repositories;

use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ClinicRepository
{
    /**
     * Get the query for getting all the items.
     */
    public function getItemsQuery(Request $request = null): Builder
    {
        $currentUser = auth()->user();

        return Clinic::query()
            /**
             * Filter by the search query, if available.
             */
            ->when(!blank($request) && $request->filled('searchQuery'), function ($query) use ($request) {
                $searchTerm = strtolower($request->get('searchQuery'));
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
            })

            /**
             * Filter by permission, if available.
             */
            ->when(!$currentUser->can('clinic.list'), function ($query) use ($currentUser) {
                $query
                    ->whereIn('clinic_id', $currentUser->clinics->pluck('clinic_id')->toArray());
            })
            ->orderBy('name');
    }
}
