<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Role;

class IsTreasurerOrIsFinancialSecretaryOrIsPresident
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->hasRole(Roles::PRESIDENT ) )
        {
            return $next($request);
        }
        if($request->user()->hasRole(Roles::FINANCIAL_SECRETARY) )
        {
            return $next($request);
        }
        if($request->user()->hasRole(Roles::TREASURER)) {
            return $next($request);
        }
        else{
            return response()->json(['message' => 'Access denied', 'status' => '403'], 403);
        }

    }
}
