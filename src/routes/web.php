<?php

use App\Http\Controllers\FinancialAccounts\DeleteAccountController;
use App\Http\Controllers\FinancialAccounts\UpdateAccountController;
use App\Http\Controllers\FinancialOperations\DeleteOperationController;
use App\Http\Controllers\UserAccountManagement\ManageUserAccountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FinancialOperations\OperationsOverviewController;
use App\Http\Controllers\FinancialAccounts\AccountsOverviewController;
use App\Http\Controllers\FinancialOperations\CreateOperationController;
use App\Http\Controllers\FinancialOperations\UpdateOperationController;
use App\Http\Controllers\FinancialOperations\OperationDetailController;
use App\Http\Controllers\SapReports\DeleteReportController;
use App\Http\Controllers\SapReports\ReportDetailController;
use App\Http\Controllers\SapReports\ReportsOverviewController;
use App\Http\Controllers\SapReports\UploadReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Authentication
 */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/login/{token}', [LoginController::class, 'loginUsingToken'])
        ->name('login-using-token');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])
        ->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLoginLink'])
        ->middleware(['ajax', 'jsonify']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware(['ajax', 'jsonify']);


/**
 * User Account Management
 */

Route::post('/change-password', [ManageUserAccountController::class, 'changePassword'])
    ->middleware(['auth', 'auth.session', 'ajax', 'jsonify']);


/**
 * Finances
 */

Route::middleware(['auth', 'auth.session'])->group(function () {
    /**
     * Financial Accounts
     */

    Route::get('/', [AccountsOverviewController::class, 'show'])
        ->name('accounts_overview');

    Route::middleware(['ajax', 'jsonify'])->group(function () {
        Route::post('/accounts', [AccountsOverviewController::class, 'createFinancialAccount']);
        
        Route::put('/accounts/{account}', [UpdateAccountController::class, 'update'])
            ->middleware('can:update,account');

        Route::delete('/accounts/{account}', [DeleteAccountController::class, 'delete'])
            ->middleware('can:delete,account');
    });


    /**
     * Financial Operations
     */

    Route::middleware('can:view,account')->group(function () {
        Route::get('/accounts/{account}/operations', [OperationsOverviewController::class, 'show']);
        Route::get('/accounts/{account}/operations/export', [OperationsOverviewController::class, 'downloadExport']);
    });

    Route::middleware('can:view,operation')->group(function () {
        Route::get('/operations/{operation}', [OperationDetailController::class, 'getOperationData']);
        Route::get('/operations/{operation}/attachment', [OperationDetailController::class, 'downloadAttachment']);
    });

    Route::middleware(['ajax', 'jsonify'])->group(function () {
        Route::post('/accounts/{account}/operations', [CreateOperationController::class, 'handleCreateOperationRequest'])
            ->middleware('can:create,App\Models\FinancialOperation,account');

        Route::middleware('can:update,operation')->group(function () {
            Route::put('/operations/{operation}', [UpdateOperationController::class, 'handleEditOperationRequest']);
            Route::patch('/operations/{operation}', [UpdateOperationController::class, 'checkOrUncheckOperation']);
        });
        
        Route::delete('/operations/{operation}', [DeleteOperationController::class, 'delete'])
            ->middleware('can:delete,operation');
    });


    /**
     * SAP Reports
     */

    Route::get('/accounts/{account}/sap-reports', [ReportsOverviewController::class, 'show'])
        ->middleware('can:view,account');

    Route::get('/sap-reports/{report}/raw', [ReportDetailController::class, 'download'])
        ->middleware('can:view,report');

    Route::middleware(['ajax', 'jsonify'])->group(function () {
        Route::post('/accounts/{account}/sap-reports', [UploadReportController::class, 'upload'])
            ->middleware('can:create,App\Models\SapReport,account');
        
        Route::delete('/sap-reports/{report}', [DeleteReportController::class, 'delete'])
            ->middleware('can:delete,report');
    });
});
