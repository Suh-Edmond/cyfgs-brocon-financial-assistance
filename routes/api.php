<?php

use App\Http\Controllers\ExpenditureCategoryController;
use App\Http\Controllers\ExpenditureDetailController;
use App\Http\Controllers\ExpenditureItemController;
use App\Http\Controllers\IncomeActivityController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\PaymentItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserContributionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSavingController;
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

Route::prefix('protected')->group(function() {
    Route::post('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'createPaymentItem']);
    Route::get('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'getPaymentItemsByCategory']);
    Route::get('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'getPaymentItem']);
    Route::put('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'updatePaymentItem']);
    Route::delete('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'deletePaymentItem']);
});


Route::post('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'createExpenditureCategory']);
Route::get('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'getExpenditureCategories']);
Route::get('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'getExpenditureCategory']);
Route::put('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'updateExpenditureCategory']);
Route::delete('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'deleteExpenditureCategory']);

Route::post('/expenditure-categories/{expenditure_category_id}/expenditure-items', [ExpenditureItemController::class, 'createExpenditureItem']);
Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items', [ExpenditureItemController::class, 'getExpenditureItems']);
Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'getExpenditureItem']);
Route::put('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'updateExpenditureItem']);
Route::delete('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'deleteExpenditureItem']);

Route::post('/user-savings', [UserSavingController::class, 'createUserSaving']);
Route::get('/user-savings/{user_id}', [UserSavingController::class, 'getUserSavings']);
Route::get('/user-savings/{user_id}/{id}', [UserSavingController::class, 'getUserSaving']);
Route::put('/user-savings/{user_id}/{id}', [UserSavingController::class, 'updateUserSaving']);
Route::delete('/user-savings/{user_id}/{id}', [UserSavingController::class, 'deleteUserSaving']);
Route::put('/user-savings/{id}', [UserSavingController::class, 'approveUserSaving']);

Route::post('organisations/{id}/income-activities', [IncomeActivityController::class, 'createIncomeActivity']);
Route::get('organisations/{id}/income-activities', [IncomeActivityController::class, 'getIncomeActivitiesByOrganisation']);
Route::get('income-activities/{id}', [IncomeActivityController::class, 'getIncomeActivity']);
Route::put('income-activities/{id}', [IncomeActivityController::class, 'updateIncomeActivity']);
Route::delete('income-activities/{id}', [IncomeActivityController::class, 'deleteIncomeActivity']);
Route::put('income-activities/{id}', [IncomeActivityController::class, 'approveIncomeActivity']);

Route::post('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'createExpenditureDetail']);
Route::get('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'updateIncomeActivity']);
Route::get('expenditure-details/{id}/', [ExpenditureDetailController::class, 'getExpenditureDetail']);
Route::put('expenditure-details/{id}', [ExpenditureDetailController::class, 'updateExpenditureDetail']);
Route::delete('expenditure-details/{id}', [ExpenditureDetailController::class, 'deleteExpenditureDetail']);


Route::post('contributions', [UserContributionController::class, 'createUserContribution']);
Route::put('contributions/{id}', [UserContributionController::class, 'updateUserContribution']);
Route::get('contributions/payment-items/{id}', [UserContributionController::class, 'getUserContributionsByItem']);
Route::get('contributions/users/{id}', [UserContributionController::class, 'getContributionByUser']);
Route::get('contributions/users/{user_id}/payment-items/{item_id}', [UserContributionController::class, 'getContributionByUserAndItem']);
Route::delete('contributions/{id}', [UserContributionController::class, 'deleteUserContributon']);
Route::put('contributions/{id}', [UserContributionController::class, 'approveUserContribution']);
Route::get('contributions/{id}', [UserContributionController::class, 'getContribution']);
Route::get('contributions', [UserContributionController::class, 'filterContribution']);
