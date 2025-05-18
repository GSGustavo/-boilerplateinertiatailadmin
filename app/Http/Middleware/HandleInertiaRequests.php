<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $auth = null;

        if (Auth::user()) {
            $authUser = Auth::user();

            $auth = [];
            $auth['user'] = $authUser;
            $auth['name'] = $authUser->name;
            $auth['is_admin'] = $authUser->is_admin;
        }

        return array_merge(parent::share($request), [
            "maxEntries" => [5, 10, 15, 30],
            "defaultEntries" => 10,
            'appurl' => env("APP_URL"),
            'auth' => $auth,
            'data' => $request->session()->get('data'),
            'message' => $request->session()->get('message'),
        ]);
    }
}
