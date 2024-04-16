<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use App\Models\CustomRole;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class isPresidentOrisFinancialSecretary
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
        if(count(collect($request->user()->roles->toArray())->whereIn('name', [Roles::MEMBER, Roles::PRESIDENT, Roles::FINANCIAL_SECRETARY])->toArray()) < 2){
            return ResponseTrait::sendError('Access denied', 'You dont have the role to access this route', 403);
        }
        return $next($request);
    }
}
