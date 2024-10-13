<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyOrganizationRequest;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\State;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
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
        return Inertia::render('panel/organizations/organizations-list', [
            'organizations' => OrganizationResource::collection($this->organizationRepository->getItemsQuery($request)->paginate(20)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/organizations/organizations-create-edit', [
            'organizationTypes' => OrganizationTypeResource::collection(OrganizationType::all()),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        return to_route('panel.organizations.show', [
            'organization' => $this->organizationRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization): Response
    {
        $organization->load([
            'organizationType',
            'state',
        ]);

        return Inertia::render('panel/organizations/organizations-create-edit', [
            'organization' => new OrganizationResource($organization),
            'organizationTypes' => OrganizationTypeResource::collection(OrganizationType::all()),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        return to_route('panel.organizations.show', [
            'organization' => $this->organizationRepository->update($request, $organization),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyOrganizationRequest $request, Organization $organization)
    {
        $this->organizationRepository->destroy($request, $organization);
        return to_route('panel.organizations.index');
    }
}
