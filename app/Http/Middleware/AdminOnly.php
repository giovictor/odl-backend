<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if($user->is_admin == 0) {
            return response()->json([
                'status' => 401,
                'message' => 'Administrator accounts only'
            ], 401);
        }

        return $next($request);
    }
}
