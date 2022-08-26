<?php

use App\Http\Controllers\ExpenditureCategoryController;
use App\Http\Controllers\ExpenditureDetailController;
use App\Http\Controllers\ExpenditureItemController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\IncomeActivityController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\PaymentItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserContributionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSavingController;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use App\Models\IncomeActivity;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use App\Models\UserContribution;
use App\Models\UserSaving;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;

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



Route::prefix('public/auth')->group(function () {
    Route::post('/login', [UserController::class, 'logInUser']);
    Route::post('/signup', [UserController::class, 'createAccount']);
});

Route::prefix('public/auth')->group(function () {
    Route::post('/set-password', [UserController::class, 'updatePassword']);
    Route::get('/check-user', [UserController::class, 'checkUserExist']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('protected/roles')->middleware('isPresident')->group(function () {
        Route::post('', [RoleController::class, 'addUserRole']);
        Route::get('', [RoleController::class, 'getAllRoles']);
        Route::get('/users/{user_id}', [RoleController::class, 'getUserRoles']);
        Route::delete('/{role_id}/users/{user_id}', [RoleController::class, 'removeUserRole']);
    });


    Route::prefix('protected/organisations')->middleware(['isPresidentOrIsFinancialSecretary'])->group(function () {
        Route::get('/{id}/users', [UserController::class, 'getUsers']);
        Route::get('/users/{id}', [UserController::class, 'getUser']);
        Route::post('{id}/add-users', [UserController::class, 'addUser']);
        Route::post('{id}/import-users', [UserController::class, 'importUsers']);
        Route::get('download-users', [UserController::class, 'downloadUsers']);
    });


    Route::prefix('protected')->middleware('isFinancialSecretary')->group(function () {
        Route::put('/users/{id}', [UserController::class, 'updateUser']);
        Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
    });


    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::post('/organisations', [OrganisationController::class, 'createOrganisation']);
        Route::get('/organisations/{id}', [OrganisationController::class, 'getOrganisation']);
        Route::get('/organisation-info', [OrganisationController::class, 'getOrganisationInfo']);
        Route::put('/organisations/{id}', [OrganisationController::class, 'updateOrgansation']);
    });


    Route::delete('protected/organisations/{id}', [OrganisationController::class, 'deleteOrganisation'])->middleware('isAdmin');


    Route::prefix('protected')->middleware(['isPresidentOrIsFinancialSecretary'])->group(function () {
        Route::post('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'createPaymentCategory']);
        Route::put('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'updatePaymentCategory']);
    });


    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretaryOrIsPresident'])->group(function () {
        Route::get('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'getPaymentCategories']);
        Route::get('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'getPaymentCategory']);
        Route::get('/download-payment-categories', [PaymentCategoryController::class, 'downloadPaymentCategory']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'deletePaymentCategory']);
    });


    Route::prefix('protected')->middleware(['isFinancialSecretary'])->group(function () {
        Route::post('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'createPaymentItem']);
        Route::put('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'updatePaymentItem']);
    });


    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'getPaymentItemsByCategory']);
        Route::get('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'getPaymentItem']);
        Route::get('download-payment-items', [PaymentItemController::class, 'downloadPaymentItem']);
    });


    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'deletePaymentItem']);
    });

    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretaryOrIsPresident'])->group(function () {
        Route::get('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'getExpenditureCategories']);
        Route::get('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'getExpenditureCategory']);
        Route::get('download-expenditure-categories', [ExpenditureCategoryController::class, 'downloadExpenditureCategory']);
    });

    Route::prefix('protected')->middleware(['isPresidentOrIsFinancialSecretary'])->group(function () {
        Route::post('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'createExpenditureCategory']);
        Route::put('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'updateExpenditureCategory']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'deleteExpenditureCategory']);
    });

    Route::prefix('protected')->middleware('isFinancialSecretary')->group(function () {
        Route::put('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'updateExpenditureItem']);
        Route::post('expenditure-categories/{id}/expenditure-items', [ExpenditureItemController::class, 'createExpenditureItem']);
    });


    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items', [ExpenditureItemController::class, 'getExpenditureItems']);
        Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'getExpenditureItem']);
        Route::get('download-expenditure-items', [ExpenditureItemController::class, 'downloadExpenditureItems']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'deleteExpenditureItem']);
    });

    Route::prefix('protected')->middleware('isTreasurer')->group(function () {
        Route::put('expenditure-items/{id}/approve', [ExpenditureItemController::class, 'approveExpenditureItem']);
    });

    Route::prefix('protected')->middleware('isFinancialSecretary')->group(function () {
        Route::post('/user-savings', [UserSavingController::class, 'createUserSaving']);
        Route::put('/user-savings/{user_id}/{id}', [UserSavingController::class, 'updateUserSaving']);
    });

    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('/user-savings/{user_id}', [UserSavingController::class, 'getUserSavings']);
        Route::get('/user-savings/{user_id}/{id}', [UserSavingController::class, 'getUserSaving']);
        Route::get('download-user-savings', [UserSavingController::class, 'downloadUserSaving']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('/user-savings/{user_id}/{id}', [UserSavingController::class, 'deleteUserSaving']);
    });

    Route::prefix('protected')->middleware('isTreasurer')->group(function () {
        Route::put('/user-savings/{id}', [UserSavingController::class, 'approveUserSaving']);
    });

    Route::prefix('protected')->middleware('isTreasurerOrIsFinancialSecretaryOrIsPresident')->group(function () {
        Route::get('/organisations/{id}/user-savings', [UserSavingController::class, 'getAllUserSavingsByOrganisation']);
        Route::get('/organisations/{id}/user-savings/approve', [UserSavingController::class, 'getUserSavingsByStatusAndOrganisation']);
    });


    Route::prefix('protected')->middleware(['isPresidentOrIsFinancialSecretary'])->group(function () {
        Route::post('/organisations/{id}/income-activities', [IncomeActivityController::class, 'createIncomeActivity']);
        Route::put('/income-activities/{id}/update', [IncomeActivityController::class, 'updateIncomeActivity']);
    });

    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('organisations/{id}/income-activities', [IncomeActivityController::class, 'getIncomeActivitiesByOrganisation']);
        Route::get('income-activities/{id}', [IncomeActivityController::class, 'getIncomeActivity']);
        Route::get('income-activities', [IncomeActivityController::class, 'filterIncomeActivity']);
    });
    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('income-activity/generate-pdf', [IncomeActivityController::class, 'generateIncomeActivityPDF']);
    });

    Route::prefix('protected')->middleware('isTreasurer')->group(function () {
        Route::put('income-activities/{id}/approve', [IncomeActivityController::class, 'approveIncomeActivity']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('income-activities/{id}', [IncomeActivityController::class, 'deleteIncomeActivity']);
    });

    Route::prefix('protected')->middleware('isFinancialSecretary')->group(function () {
        Route::post('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'createExpenditureDetail']);
        Route::put('expenditure-details/{id}/update', [ExpenditureDetailController::class, 'updateExpenditureDetail']);
    });


    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'getExpenditureDetails']);
        Route::get('expenditure-details/{id}', [ExpenditureDetailController::class, 'getExpenditureDetail']);
        Route::get('expenditure-details', [ExpenditureDetailController::class, 'filterExpenditureDetails']);
        Route::get('download-expenditure-details', [ExpenditureDetailController::class, 'downloadExpenditureDetail']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('expenditure-details/{id}', [ExpenditureDetailController::class, 'deleteExpenditureDetail']);
    });

    Route::prefix('protected')->middleware('isTreasurer')->group(function () {
        Route::put('expenditure-details/{id}', [ExpenditureDetailController::class, 'approveExpenditureDetail']);
    });


    Route::prefix('protected')->middleware('isFinancialSecretary')->group(function () {
        Route::post('contributions', [UserContributionController::class, 'createUserContribution']);
        Route::put('contributions/{id}/update', [UserContributionController::class, 'updateUserContribution']);
    });

    Route::prefix('protected')->middleware(['isTreasurerOrIsFinancialSecretary'])->group(function () {
        Route::get('contributions/payment-items/{id}', [UserContributionController::class, 'getUserContributionsByItem']);
        Route::get('contributions/users/{id}', [UserContributionController::class, 'getContributionByUser']);
        Route::get('contributions/users/{user_id}/items/{id}', [UserContributionController::class, 'getContributionByUserAndItem']);
        Route::get('contributions/{id}', [UserContributionController::class, 'getContribution']);
        Route::get('contributions', [UserContributionController::class, 'filterContribution']);
        Route::get('user-contributions/month', [UserContributionController::class, 'filterContributionByMonth']);
        Route::get('user-contributions/year', [UserContributionController::class, 'filterContributionByYear']);
        Route::get('download-contributions', [UserContributionController::class, 'downloadContrition']);
    });

    Route::prefix('protected')->middleware('isTreasurer')->group(function () {
        Route::put('contributions/{id}', [UserContributionController::class, 'approveUserContribution']);
    });

    Route::prefix('protected')->middleware('isPresident')->group(function () {
        Route::delete('contributions/{id}', [UserContributionController::class, 'deleteUserContributon']);
    });


    Route::prefix('protected')->middleware('isUser')->group(function () {
        Route::post('upload-file', [FileUploadController::class, 'uploadFile']);
        Route::get('fetch-file', [FileUploadController::class, 'getUploadFile']);
    });
});
