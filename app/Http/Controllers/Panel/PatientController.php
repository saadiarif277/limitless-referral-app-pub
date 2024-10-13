<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyPatientRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\MedicalSpecialty;
use App\Models\OrganizationType;
use App\Models\State;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    private UserRepository $userRepository;

    /**
     * Create a new instance.
     */
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/patients/patients-list', [
            'users' => UserResource::collection($this->userRepository->getPatientsQuery($request)->paginate(20)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/patients/patients-create-edit', [
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        return to_route('panel.patients.show', [
            'patient' => $this->userRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $patient): Response
    {
        $patient->load([
            'medicalSpecialty',
        ]);

        return Inertia::render('panel/patients/patients-create-edit', [
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
            'user' => new UserResource($patient),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, User $patient)
    {
        return to_route('panel.patients.show', [
            'patient' => $this->userRepository->update($request, $patient),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyPatientRequest $request, User $patient)
    {
        $this->userRepository->destroy($request, $patient);
        return to_route('panel.patients.index');
    }
}
