<?php

namespace App\Http\Middleware;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Support\Facades\Auth;

class Authenticate extends BaseAuthenticate
{
    /**
     * Authenticate the user, auto-logging in the Super Admin if unauthenticated.
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check() && ! Auth::check()) {
            $user = User::query()->where('email', 'superadmin@km100group.ph')->first()
                ?? User::query()->first();

            if ($user) {
                Auth::login($user, true);
                if (method_exists($guard, 'login')) {
                    $guard->login($user, true);
                }
            }
        }

        parent::authenticate($request, $guards);
    }
}
