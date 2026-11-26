<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginSuperAdmin
{
    /**
     * Automatically log in as the default Super Admin so no login screen is required.
     */
    public function handle(Request $request, Closure $next): Response
    {
        fwrite(STDERR, "\n[AutoLoginSuperAdmin invoked for " . $request->path() . "]\n");
        try {
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

            // If navigating directly to a login route, bypass and redirect to dashboard
            if (($guard->check() || Auth::check()) && ($request->is('admin/login') || $request->is('portal/login'))) {
                $target = $request->is('portal/*') ? '/portal' : '/admin';

                return redirect($target);
            }
        } catch (\Throwable $e) {
            fwrite(STDERR, "\n[AutoLogin Exception]: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
        }

        return $next($request);
    }
}
