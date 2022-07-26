<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
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
    Route::delete('/{role_id}/users/{user_id}', [RoleController::class, 'removeUserRole']);
    Route::get('', [RoleController::class, 'getAllRoles']);
    Route::get('users/{user_id}', [RoleController::class, 'getUserRoles']);
});
