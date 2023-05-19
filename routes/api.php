<?php

use App\Http\Controllers\ActivitySupportController;
use App\Http\Controllers\ExpenditureCategoryController;
use App\Http\Controllers\ExpenditureDetailController;
use App\Http\Controllers\ExpenditureItemController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\GenerateReportController;
use App\Http\Controllers\IncomeActivityController;
use App\Http\Controllers\MemberRegistrationController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\PaymentItemController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SessionController;
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



Route::prefix('public/auth')->group(function () {
    Route::post('/login', [UserController::class, 'logInUser']);
    Route::post('/signup', [UserController::class, 'createAccount']);
});

Route::prefix('public/auth')->group(function () {
    Route::post('/set-password', [UserController::class, 'setPassword']);
    Route::post('/check-user', [UserController::class, 'checkUserExist']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('protected/roles')->group(function () {
        Route::post('', [RoleController::class, 'addUserRole']);
        Route::get('', [RoleController::class, 'getAllRoles']);
        Route::get('/users/{user_id}', [RoleController::class, 'getUserRoles']);
        Route::delete('/users/{user_id}', [RoleController::class, 'removeUserRole']);
    });


    Route::prefix('protected/organisations')->group(function () {
        Route::get('/{id}/users', [UserController::class, 'getUsers']);
        Route::get('/users/{id}', [UserController::class, 'getUser']);
        Route::post('{id}/add-users', [UserController::class, 'addUser']);
        Route::post('{id}/import-users', [UserController::class, 'importUsers']);
        Route::get('download-users', [UserController::class, 'downloadUsers']);
        Route::get('filter-users', [UserController::class, 'filterUsers']);
    });


    Route::prefix('protected')->group(function () {
        Route::put('/users/{id}', [UserController::class, 'updateUser']);
        Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
        Route::put('/user/profile/update', [UserController::class, 'updateProfile']);
        Route::put('/user/profile/password/update', [UserController::class, 'updatePassword']);
    });


    Route::prefix('protected')->group(function () {
        Route::post('/organisations', [OrganisationController::class, 'createOrganisation']);
        Route::get('/organisations/{id}', [OrganisationController::class, 'getOrganisation']);
        Route::put('/organisations/{id}', [OrganisationController::class, 'updateOrganisation']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('/organisation-info', [OrganisationController::class, 'getOrganisationInfo']);
    });


    Route::delete('protected/organisations/{id}', [OrganisationController::class, 'deleteOrganisation'])->middleware('isAdmin');


    Route::prefix('protected')->group(function () {
        Route::post('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'createPaymentCategory']);
        Route::put('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'updatePaymentCategory']);
    });


    Route::prefix('protected')->group(function () {
        Route::get('/organisations/{organisation_id}/payment-categories', [PaymentCategoryController::class, 'getPaymentCategories']);
        Route::get('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'getPaymentCategory']);
        Route::get('/download-payment-categories', [PaymentCategoryController::class, 'downloadPaymentCategory']);
        Route::get('/filter-payment-categories', [PaymentCategoryController::class, 'filterPaymentCategory']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('/organisations/{organisation_id}/payment-categories/{id}', [PaymentCategoryController::class, 'deletePaymentCategory']);
    });


    Route::prefix('protected')->group(function () {
        Route::post('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'createPaymentItem']);
        Route::put('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'updatePaymentItem']);
    });


    Route::prefix('protected')->group(function () {
        Route::get('/payment-items', [PaymentItemController::class, 'getAllPaymentItems']);
        Route::get('/payment-categories/{payment_category_id}/payment-items', [PaymentItemController::class, 'getPaymentItemsByCategory']);
        Route::get('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'getPaymentItem']);
        Route::get('download-payment-items', [PaymentItemController::class, 'downloadPaymentItem']);
        Route::get('filter-payment-items', [PaymentItemController::class, 'filterPaymentItems']);
        Route::get('/payment-items/type', [PaymentItemController::class, 'getPaymentItemByType']);
        Route::put('/payment-items/update/reference', [PaymentItemController::class, 'updatePaymentItemReference']);
        Route::get('/payment-items/{id}/references', [PaymentItemController::class, 'getPaymentItemReferences']);
    });


    Route::prefix('protected')->group(function () {
        Route::delete('/payment-categories/{payment_category_id}/payment-items/{id}', [PaymentItemController::class, 'deletePaymentItem']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'getExpenditureCategories']);
        Route::get('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'getExpenditureCategory']);
        Route::get('expenditure-categories/download', [ExpenditureCategoryController::class, 'downloadExpenditureCategory']);
        Route::get('expenditure-categories/filter', [ExpenditureCategoryController::class, 'filterExpenditureCategories']);
    });

    Route::prefix('protected')->group(function () {
        Route::post('/organisations/{organisation_id}/expenditure-categories', [ExpenditureCategoryController::class, 'createExpenditureCategory']);
        Route::put('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'updateExpenditureCategory']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('/organisations/{organisation_id}/expenditure-categories/{id}', [ExpenditureCategoryController::class, 'deleteExpenditureCategory']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'updateExpenditureItem']);
        Route::post('expenditure-categories/{id}/expenditure-items', [ExpenditureItemController::class, 'createExpenditureItem']);
    });


    Route::prefix('protected')->group(function () {
        Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items', [ExpenditureItemController::class, 'getExpenditureItems']);
        Route::get('/expenditure-categories/{expenditure_category_id}/expenditures', [ExpenditureItemController::class, 'getExpenditureByCategory']);
        Route::get('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'getExpenditureItem']);
        Route::get('/expenditure-items/{id}', [ExpenditureItemController::class, 'getItem']);
        Route::get('expenditure_categories/expenditure_items/download', [ExpenditureItemController::class, 'downloadExpenditureItems']);
        Route::get('/expenditure-items/payment-items/{payment_item_id}', [ExpenditureItemController::class, 'getExpenditureItemByPaymentItem']);
        Route::get('/expenditure_categories/expenditure_items/filter', [ExpenditureItemController::class, 'filterExpenditureItems']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('/expenditure-categories/{expenditure_category_id}/expenditure-items/{id}', [ExpenditureItemController::class, 'deleteExpenditureItem']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('expenditure-items/{id}/approve', [ExpenditureItemController::class, 'approveExpenditureItem']);
    });

    Route::prefix('protected')->group(function () {
        Route::post('/user-savings', [UserSavingController::class, 'createUserSaving']);
        Route::put('/user-savings/{user_id}/{id}', [UserSavingController::class, 'updateUserSaving']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('/user-savings/{user_id}', [UserSavingController::class, 'getUserSavings']);
        Route::get('/user-savings/{user_id}/{id}', [UserSavingController::class, 'getUserSaving']);
        Route::get('savings/download', [UserSavingController::class, 'download']);
        Route::get('organisation-savings/download', [UserSavingController::class, 'downloadOrganisationSavings']);
        Route::get('savings', [UserSavingController::class, 'getMembersSavingsByName']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('/user-savings/{user_id}/{id}', [UserSavingController::class, 'deleteUserSaving']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('/user-savings/{id}', [UserSavingController::class, 'approveUserSaving']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('/savings', [UserSavingController::class, 'getAllUserSavingsByOrganisation']);
        Route::get('/organisations/{id}/user-savings/approve', [UserSavingController::class, 'getUserSavingsByStatusAndOrganisation']);
        Route::get('/user-savings', [UserSavingController::class, 'filterSavings']);
    });


    Route::prefix('protected')->group(function () {
        Route::post('/organisations/{id}/income-activities', [IncomeActivityController::class, 'createIncomeActivity']);
        Route::put('/income-activities/{id}/update', [IncomeActivityController::class, 'updateIncomeActivity']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('organisations/{id}/income-activities', [IncomeActivityController::class, 'getIncomeActivitiesByOrganisation']);
        Route::get('income-activities/{id}', [IncomeActivityController::class, 'getIncomeActivity']);
        Route::get('organisations/income-activities/search', [IncomeActivityController::class, 'filterIncomeActivity']);
    });
    Route::prefix('protected')->group(function () {
        Route::get('organisations/income-activities/generate-pdf', [IncomeActivityController::class, 'generateIncomeActivityPDF']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('income-activities/{id}/approve', [IncomeActivityController::class, 'approveIncomeActivity']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('income-activities/{id}', [IncomeActivityController::class, 'deleteIncomeActivity']);
    });

    Route::prefix('protected')->group(function () {
        Route::post('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'createExpenditureDetail']);
        Route::put('expenditure-details/{id}/update', [ExpenditureDetailController::class, 'updateExpenditureDetail']);
    });


    Route::prefix('protected')->group(function () {
        Route::get('expenditure-items/{id}/details', [ExpenditureDetailController::class, 'getExpenditureDetails']);
        Route::get('expenditure-details/{id}', [ExpenditureDetailController::class, 'getExpenditureDetail']);
        Route::get('expenditure-details', [ExpenditureDetailController::class, 'filterExpenditureDetails']);
        Route::get('expenditure-item-details/download', [ExpenditureDetailController::class, 'downloadExpenditureDetail']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('expenditure-item-details/{id}', [ExpenditureDetailController::class, 'deleteExpenditureDetail']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('expenditure-details/{id}/approve', [ExpenditureDetailController::class, 'approveExpenditureDetail']);
    });


    Route::prefix('protected')->group(function () {
        Route::post('contributions', [UserContributionController::class, 'createUserContribution']);
        Route::put('contributions/{id}/update', [UserContributionController::class, 'updateUserContribution']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('contributions/payment-items/{id}/users/{user_id}', [UserContributionController::class, 'getUsersContributionsByItem']);
        Route::get('contributions/payment-items/{id}', [UserContributionController::class, 'getContributionsByPaymentItem']);
        Route::get('contributions/users/{id}', [UserContributionController::class, 'getContributionByUser']);
        Route::get('contributions/users/{user_id}/items/{id}', [UserContributionController::class, 'getTotalAmountPaidByUserForTheItem']);
        Route::get('contributions/{id}', [UserContributionController::class, 'getContribution']);
        Route::get('organisations/contributions/search', [UserContributionController::class, 'filterContributions']);
        Route::get('download-contributions', [UserContributionController::class, 'downloadFilteredContributions']);
        Route::get('download-user-contributions', [UserContributionController::class, 'downloadUserContributions']);
        Route::post('contributions/bulk-payments', [UserContributionController::class, 'bulkPayment']);
        Route::get('contributions/members/debts', [UserContributionController::class, 'getMemberOweContributions']);
        Route::get('contributions/members/paid', [UserContributionController::class, 'getAllMemberContributions']);
    });

    Route::prefix('protected')->group(function () {
        Route::put('contributions/approve', [UserContributionController::class, 'approveUserContribution']);
    });

    Route::prefix('protected')->group(function () {
        Route::delete('contributions/{id}', [UserContributionController::class, 'deleteUserContributon']);
    });


    Route::prefix('protected')->middleware(['isUser'])->group(function () {
        Route::post('upload-file', [FileUploadController::class, 'uploadFile']);
        Route::get('fetch-file', [FileUploadController::class, 'getUploadFile']);
    });

    Route::prefix('protected')->group(function () {
        Route::get('sponsorships', [ActivitySupportController::class, 'fetchAll']);
        Route::post('/activity-supports', [ActivitySupportController::class, 'createActivitySupport']);
        Route::put('activity-supports/{id}', [ActivitySupportController::class, 'updateActivitySupport']);
        Route::get('/activity-supports/{id}', [ActivitySupportController::class, 'fetchActivitySupport']);
        Route::get('/activity-supports/payment-items/{id}', [ActivitySupportController::class, 'getActivitySupportsByPaymentItem']);
        Route::delete('sponsorships/{id}', [ActivitySupportController::class, 'deleteActivitySupport']);
        Route::get('sponsorships/download', [ActivitySupportController::class, 'downloadActivitySupport']);
        Route::get('sponsorships/search', [ActivitySupportController::class, 'filterActivitySupport']);
        Route::put('sponsorships/{id}/approve', [ActivitySupportController::class, 'changeActivityState']);
    });
    Route::prefix('protected')->group(function() {
        Route::get('/activity/{id}/generate-report', [GenerateReportController::class, 'generateReportByActivity']);
        Route::get('/activity/{id}/report/download', [GenerateReportController::class, 'downloadReportByActivity']);
        Route::get('/generate-quarterly-report', [GenerateReportController::class, 'generateQuarterlyReport']);
    });

    Route::prefix('protected')->group(function() {
        Route::post('/members-registration', [MemberRegistrationController::class, 'addRegistration']);
        Route::put('/members-registration/update', [MemberRegistrationController::class, 'updateRegistration']);
        Route::get('/registered-members', [MemberRegistrationController::class, 'getRegistrations']);
        Route::delete('/registered-members/{id}', [MemberRegistrationController::class, 'deleteRegistration']);
        Route::put('/members-registration/approve', [MemberRegistrationController::class, 'approveRegisteredMember']);
        Route::get('/registered-members/download', [MemberRegistrationController::class, 'downloadRegisteredMembers']);
    });

    Route::prefix('protected')->group(function (){
        Route::post('/sessions', [SessionController::class, 'createSession']);
        Route::get('/sessions', [SessionController::class, 'getAllSessions']);
        Route::get('/sessions/current', [SessionController::class, 'getCurrentSession']);
        Route::put('/sessions/update', [SessionController::class, 'updateSession']);
        Route::delete('sessions/{id}', [SessionController::class, 'deleteSession']);
    });

    Route::prefix('protected')->group(function() {
        Route::post('/registration_fees', [RegistrationController::class, 'createRegFee']);
        Route::put('/registration_fees/{id}', [RegistrationController::class, 'updateRegFee']);
        Route::get('/registration_fees', [RegistrationController::class, 'getAllRegistrationFee']);
        Route::get('/registration_fees/current', [RegistrationController::class, 'getCurrentRegistrationFee']);
        Route::delete('/registration_fees/{id}', [RegistrationController::class, 'deleteRegistrationFee']);
        Route::put('/registration_fees/{id}/set', [RegistrationController::class, 'setNewFee']);
    });
});

