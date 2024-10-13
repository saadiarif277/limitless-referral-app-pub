<?php

namespace App\Http\Controllers\Panel;

use App\Models\ReferralType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReferralTypeResource;
use App\Http\Requests\DestroyReferralTypeRequest;
use App\Http\Requests\StoreReferralTypeRequest;
use App\Http\Requests\UpdateReferralTypeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReferralTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/referral-types/referral-types-list', [
            'referralTypes' => ReferralTypeResource::collection(
                ReferralType::query()
                    ->withCount(['referrals'])
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('name')
                    ->paginate(10)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/referral-types-create-edit', [
            // ...
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralTypeRequest $request)
    {
        $referralType = ReferralType::create($request->safe()->only([
            'name',
            'description',
        ]));

        return to_route('panel.referral-types.show', [
            'referral_type' => $referralType->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ReferralType $referralType): Response
    {
        return Inertia::render('panel/referral-types/referral-types-create-edit', [
            'referralType' => new ReferralTypeResource($referralType),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReferralTypeRequest $request, ReferralType $referralType)
    {
        $referralType->update($request->safe()->only([
            'name',
            'description',
        ]));

        return to_route('panel.referral-types.show', [
            'referral_type' => $referralType->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyReferralTypeRequest $request, ReferralType $referralType)
    {
        $referralType->delete();
        return to_route('panel.referral-types.index');
    }
}
