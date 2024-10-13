<?php

namespace App\Http\Controllers\Panel;

use App\Models\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\StateResource;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
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
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('panel/profile/profile-edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'states' => StateResource::collection(
                State::query()
                    ->orderBy('name')
                    ->get()
            ),
            'timezoneOptions' => $this->userRepository->getTimezoneOptions(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $payload = [
            // General Information
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'birthdate' => $request->get('birthdate'),

            // Location Information
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
            'timezone' => $request->get('timezone'),
            
            // Personal Health Information
            'gender' => $request->get('gender'),
            'height' => $request->get('height'),
            'weight' => $request->get('weight'),
        ];

        if ($request->user()->isDirty('email')) {
            $payload['email_verified_at'] = null;
        }

        if ($request->filled('password')) {
            $payload['password'] = bcrypt($request->get('password'));
        }

        auth()->user()->update($payload);

        return Redirect::route('panel.me.profile.edit');
    }
}
