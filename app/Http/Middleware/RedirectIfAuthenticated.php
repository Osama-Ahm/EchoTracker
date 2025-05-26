public function handle($request, Closure $next, ...$guards)
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            $user = Auth::guard($guard)->user();
            
            // If user is an authority but doesn't have an authority_id, redirect to setup
            if ($user->role === 'authority' && !$user->authority_id) {
                return redirect()->route('authorities.setup');
            }
            
            return redirect(RouteServiceProvider::HOME);
        }
    }

    return $next($request);
}