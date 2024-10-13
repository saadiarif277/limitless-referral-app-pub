<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $impersonationManager = app('impersonate');
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'userResource' => $request->user() ? new \App\Http\Resources\UserResource($request->user()) : null,
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                'isImpersonated' => $request->user() ? $impersonationManager->findUserById($request->user()->getKey())->isImpersonated() : false,
            ],
            'query' => $request->query(),
        ];
    }
}
