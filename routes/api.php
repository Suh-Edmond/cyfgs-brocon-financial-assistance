<?php

use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('protected/roles')->group(function () {
    Route::post('', [RoleController::class, 'addUserRole']);
    Route::get('', [RoleController::class, 'getAllRoles']);
    Route::get('/users/{user_id}', [RoleController::class, 'getUserRoles']);
    Route::delete('/{role_id}/users/{user_id}', [RoleController::class, 'removeUserRole']);
});

Route::prefix('protected')->group(function() {
    Route::post('/users', [UserController::class, 'createUser']);
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/users/{id}', [UserController::class, 'getUser']);
    Route::put('/users/{id}', [UserController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
});

Route::prefix('protected')->group(function() {
    Route::post('/organisations', [OrganisationController::class, 'createOrganisation']);
    Route::get('/organisations/{id}', [OrganisationController::class, 'getOrganisation']);
    Route::get('/organisation-info', [OrganisationController::class, 'getOrganisationInfo']);
    Route::put('/organisations/{id}', [OrganisationController::class, 'updateOgransation']);
    Route::delete('/organisations/{id}', [OrganisationController::class, 'deleteOrganisation']);
});

Route::prefix('protected')->group(function() {
    Route::post('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'createPaymentCategory']);
    Route::get('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'getPaymentCategories']);
    Route::get('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'getPaymentCategory']);
    Route::put('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'updatePaymentCategory']);
    Route::delete('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'deletePaymentCategory']);
});
