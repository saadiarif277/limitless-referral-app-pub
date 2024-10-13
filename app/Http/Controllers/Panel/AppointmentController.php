<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentTypeResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Referral;
use App\Models\ReferralStatus;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\AppointmentRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    private AppointmentRepository $appointmentRepository;
    private OrganizationRepository $organizationRepository;
    private UserRepository $userRepository;

    /**
     * Create a new instance.
     */
    public function __construct(
        AppointmentRepository $appointmentRepository,
        OrganizationRepository $organizationRepository,
        UserRepository $userRepository,
    ) {
        $this->appointmentRepository = $appointmentRepository;
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/appointments/appointments-list', [
            'appointments' => AppointmentResource::collection($this->appointmentRepository->getItemsQuery($request)->get()),
            'organizationTypes' => OrganizationTypeResource::collection(OrganizationType::with(['organizations'])->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $currentUser = auth()->user();

        return Inertia::render('panel/appointments/appointments-create-edit', [
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'organizations' => OrganizationResource::collection($currentUser->organizations),
            'patients' => UserResource::collection($this->userRepository->getPatientsQuery($request)->get()),
            'referral' => $request->filled('referral_id')
                ? new ReferralResource(Referral::with(['patientUser'])->findOrFail($request->get('referral_id')))
                : NULL,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        return to_route('panel.appointment.show', [
            'appointment' => $this->appointmentRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $currentUser = auth()->user();
        $appointment->load(['referral']);

        $organizations = Organization::query()
            ->whereIn('organization_id', array_unique(
                array_merge(
                    [$appointment->organization_id],
                    $currentUser->organizations->pluck('organization_id')->toArray()
                ))
            )
            ->get();

        return Inertia::render('panel/appointments/appointments-create-edit', [
            'appointment' => new AppointmentResource($appointment),
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'patients' => UserResource::collection(User::all()),
            'organizations' => OrganizationResource::collection($organizations),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        return to_route('panel.appointments.show', [
            'appointment' => $this->appointmentRepository->update($request, $appointment),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $this->appointmentRepository->destroy($request, $appointment);
        return to_route('panel.appointments.index');
    }
}
