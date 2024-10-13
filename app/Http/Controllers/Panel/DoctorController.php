<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyDoctorRequest;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\MedicalSpecialty;
use App\Models\OrganizationType;
use App\Models\State;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
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
        return Inertia::render('panel/doctors/doctors-list', [
            'users' => UserResource::collection($this->userRepository->getDoctorsQuery($request)->paginate(20)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/doctors/doctors-create-edit', [
            'medicalSpecialties' => MedicalSpecialtyResource::collection(MedicalSpecialty::all()),
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        return to_route('panel.doctors.show', [
            'doctor' => $this->userRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $doctor): Response
    {
        $doctor->load([
            'medicalSpecialty',
        ]);

        return Inertia::render('panel/doctors/doctors-create-edit', [
            'medicalSpecialties' => MedicalSpecialtyResource::collection(MedicalSpecialty::all()),
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
            'user' => new UserResource($doctor),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, User $doctor)
    {
        return to_route('panel.doctors.show', [
            'doctor' => $this->userRepository->update($request, $doctor),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyDoctorRequest $request, User $doctor)
    {
        $this->userRepository->destroy($request, $doctor);
        return to_route('panel.doctors.index');
    }
}
