<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyLawFirmRequest;
use App\Http\Requests\StoreLawFirmRequest;
use App\Http\Requests\UpdateLawFirmRequest;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\State;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LawFirmController extends Controller
{
    /**
     * Create a new instance.
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/law-firms/law-firms-list', [
            'organizations' => OrganizationResource::collection(
                $this->organizationRepository
                    ->getItemsQuery($request)
                    ->where('organizations.organization_type_id', OrganizationType::LEGAL)
                    ->paginate(20)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/law-firms/law-firms-create-edit', [
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLawFirmRequest $request)
    {
        return to_route('panel.law-firms.show', [
            'law_firm' => $this->organizationRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $lawFirm): Response
    {
        return Inertia::render('panel/law-firms/law-firms-create-edit', [
            'organization' => new OrganizationResource($lawFirm),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLawFirmRequest $request, Organization $lawFirm)
    {
        return to_route('panel.law-firms.show', [
            'law_firm' => $this->organizationRepository->update($request, $lawFirm),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyLawFirmRequest $request, Organization $lawFirm)
    {
        $this->organizationRepository->destroy($request, $lawFirm);
        return to_route('panel.law-firms.index');
    }
}
