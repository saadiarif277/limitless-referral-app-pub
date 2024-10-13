<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTypeResource;
use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\MedicalSpecialty;
use App\Models\OrganizationType;
use APp\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    private UserRepository $userRepository;

    /**
     * Create a new instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/users/users-list', [
            'users' => UserResource::collection($this->userRepository->getItemsQuery($request)->paginate(20)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/users/users-create-edit', [
            'medicalSpecialties' => MedicalSpecialtyResource::collection(MedicalSpecialty::all()),
            'roles' => RoleResource::collection(Role::all()),
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
            'userTypes' => UserTypeResource::collection(UserType::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        return to_route('panel.users.show', [
            'user' => $this->userRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user): Response
    {
        $user->load([
            'roles',
            'medicalSpecialty',
            'organizations',
        ]);

        return Inertia::render('panel/users/users-create-edit', [
            'medicalSpecialties' => MedicalSpecialtyResource::collection(MedicalSpecialty::all()),
            'organizationTypes' => OrganizationTypeResource::collection(OrganizationType::with(['organizations'])->get()),
            'roles' => RoleResource::collection(Role::all()),
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
            'userTypes' => UserTypeResource::collection(UserType::all()),
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        return to_route('panel.users.show', [
            'user' => $this->userRepository->update($request, $user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        $this->userRepository->destroy($request, $user);
        return to_route('panel.users.index');
    }
}
