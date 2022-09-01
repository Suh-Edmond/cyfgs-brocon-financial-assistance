<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use App\Traits\ResponseTrait;
use Closure;

class IsAdminMiddleware
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->user()->hasRole(Roles::ADMIN)){
            return $next($request);
        }

        return ResponseTrait::sendError('Access denied', 'You dont have the role to access this route', 403);

    }
}
