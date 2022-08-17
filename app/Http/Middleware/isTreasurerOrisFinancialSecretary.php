<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class IsTreasurerOrIsFinancialSecretary
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->hasRole(Roles::TREASURER))
        {
            return $next($request);
        }
        else if($request->user()->hasRole(Roles::FINANCIAL_SECRETARY))
        {
            return $next($request);
        }
        else{
            return ResponseTrait::sendError('Access denied', 'You dont have the role to access this route', 403);
        }
    }
}
