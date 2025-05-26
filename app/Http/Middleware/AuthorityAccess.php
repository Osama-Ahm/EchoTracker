<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorityAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user is an admin or has authority role
        if ($user->isAdmin() || $user->role === 'authority') {
            // If authority role, check if they have an associated authority
            if ($user->role === 'authority' && !$user->authority) {
                return redirect()->route('authorities.setup')
                    ->with('warning', 'Please complete your authority profile setup.');
            }
            
            return $next($request);
        }
        
        abort(403, 'You do not have access to the Authorities Portal.');
    }
}

