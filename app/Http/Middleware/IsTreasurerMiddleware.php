<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use App\Models\CustomRole;
use App\Traits\ResponseTrait;
use Closure;

class IsTreasurerMiddleware
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
        if(count(collect($request->user()->roles->toArray())->whereIn('name', [Roles::MEMBER, Roles::TREASURER])->toArray()) < 2){
            return ResponseTrait::sendError('Access denied', 'You dont have the role to access this route', 403);
        }
        return $next($request);
    }
}
