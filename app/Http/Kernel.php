<?php

namespace App\Http;

use App\Http\Middleware\IsAdminIsPresidentIsFinancialSecretary;
use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\IsAuditorMiddleware;
use App\Http\Middleware\IsElectionAdmin;
use App\Http\Middleware\IsFinancialSecretaryMiddleware;
use App\Http\Middleware\IsPresidentIsFinancialSecretaryIsTreasurerIsAdmin;
use App\Http\Middleware\isPresidentMiddleware;
use App\Http\Middleware\IsPresidentOrIsAdmin;
use App\Http\Middleware\isPresidentOrisFinancialSecretary;
use App\Http\Middleware\IsTreasurerMiddleware;
use App\Http\Middleware\IsTreasurerOrIsFinancialSecretary;
use App\Http\Middleware\IsTreasurerOrIsFinancialSecretaryOrIsPresident;
use App\Http\Middleware\IsUserMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                  => \App\Http\Middleware\Authenticate::class,
        'auth.basic'            => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'              => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers'         => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                   => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'                 => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'      => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'                => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'              => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'              => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // 'role'                  => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        // 'permission'            => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        // 'role_or_permission'    => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,

        'isPresident'           => isPresidentMiddleware::class,
        'isAdmin'               => IsAdminMiddleware::class,
        'isAuditor'             => IsAuditorMiddleware::class,
        'isFinancialSecretary'  => IsFinancialSecretaryMiddleware::class,
        'isTreasurer'           => IsTreasurerMiddleware::class,
        'isUser'                => IsUserMiddleware::class,
        'isPresidentOrIsFinancialSecretary' => isPresidentOrisFinancialSecretary::class,
        'isTreasurerOrIsFinancialSecretary' => IsTreasurerOrIsFinancialSecretary::class,
        'isTreasurerOrIsFinancialSecretaryOrIsPresident' => IsTreasurerOrIsFinancialSecretaryOrIsPresident::class,
        'isElectionAdmin' => IsElectionAdmin::class,
        'IsPresidentIsFinancialSecretaryIsTreasurerIsAdmin' => IsPresidentIsFinancialSecretaryIsTreasurerIsAdmin::class,
        'isPresidentOrIsAdmin' => IsPresidentOrIsAdmin::class,
        'isAdminIsPresidentIsFinancialSecretary' => IsAdminIsPresidentIsFinancialSecretary::class

    ];
}
