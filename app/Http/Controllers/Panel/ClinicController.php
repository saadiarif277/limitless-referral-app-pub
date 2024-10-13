<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyClinicRequest;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\State;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClinicController extends Controller
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
        return Inertia::render('panel/clinics/clinics-list', [
            'organizations' => OrganizationResource::collection(
                $this->organizationRepository
                    ->getItemsQuery($request)
                    ->where('organizations.organization_type_id', OrganizationType::CLINICS_MEDICAL_OFFICES)
                    ->paginate(20)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/clinics/clinics-create-edit', [
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicRequest $request)
    {
        return to_route('panel.clinics.show', [
            'clinic' => $this->organizationRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $clinic): Response
    {
        return Inertia::render('panel/clinics/clinics-create-edit', [
            'organization' => new OrganizationResource($clinic),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicRequest $request, Organization $clinic)
    {
        return to_route('panel.clinics.show', [
            'clinic' => $this->organizationRepository->update($request, $clinic),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyClinicRequest $request, Organization $clinic)
    {
        $this->organizationRepository->destroy($request, $clinic);
        return to_route('panel.clinics.index');
    }
}
