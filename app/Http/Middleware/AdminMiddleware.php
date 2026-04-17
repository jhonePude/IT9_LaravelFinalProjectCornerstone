<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is logged in but is NOT Role 1 (Admin), send them to their portal
        if (auth()->check() && auth()->user()->Role_Id != 1) {
            return redirect()->route('member.portal')->with('error', 'Admins only.');
        }

        return $next($request);
    }
}
