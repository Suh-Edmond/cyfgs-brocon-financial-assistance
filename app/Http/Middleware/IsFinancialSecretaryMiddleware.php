<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use Closure;

class IsFinancialSecretaryMiddleware
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
        if(! $request->user()->hasRole(Roles::FINANCIAL_SECRETARY)){
            return response()->json(['message' => 'Access denied', 'status' => '403'], 403);
        }
        return $next($request);
    }
}
