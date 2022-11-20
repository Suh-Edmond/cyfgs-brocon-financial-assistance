<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use App\Models\CustomRole;
use App\Traits\ResponseTrait;
use Closure;

class IsFinancialSecretaryMiddleware
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
        if($request->user()->hasRole(CustomRole::findByName(Roles::FINANCIAL_SECRETARY, 'api'))){
            return $next($request);
        }

        return ResponseTrait::sendError('Access denied', 'You dont have the role to access this route', 403);

    }
}
