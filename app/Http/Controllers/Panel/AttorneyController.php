<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyAttorneyRequest;
use App\Http\Requests\StoreAttorneyRequest;
use App\Http\Requests\UpdateAttorneyRequest;
use App\Models\OrganizationType;
use App\Models\State;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttorneyController extends Controller
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
        return Inertia::render('panel/attorneys/attorneys-list', [
            'users' => UserResource::collection($this->userRepository->getAttorneysQuery($request)->paginate(20)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/attorneys/attorneys-create-edit', [
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttorneyRequest $request)
    {
        return to_route('panel.attorneys.show', [
            'attorney' => $this->userRepository->store($request),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $attorney): Response
    {
        return Inertia::render('panel/attorneys/attorneys-create-edit', [
            'states' => StateResource::collection(State::all()),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
            'user' => new UserResource($attorney),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttorneyRequest $request, User $attorney)
    {
        return to_route('panel.attorneys.show', [
            'attorney' => $this->userRepository->update($request, $attorney),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyAttorneyRequest $request, User $attorney)
    {
        $this->userRepository->destroy($request, $attorney);
        return to_route('panel.attorneys.index');
    }
}
