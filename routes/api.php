<?php

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

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => [], 'prefix' => 'test'], function () {
    Route::post('/', 'TestController@post');
    Route::get('/', 'TestController@get');
});

Route::group(['namespace' => 'App\Http\Controllers\Auth', 'prefix' => 'auth'], function () {
    Route::post('login', 'LoginController@post');
    Route::post('logout', 'LogoutController@post');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Auth', 'prefix' => 'auth'], function () {
    Route::post('forgotpassword', 'ForgotPasswordController@forgot');
    Route::post('forgotpassword/reset', 'ForgotPasswordController@reset');
    Route::post('forgotpassword/find', 'ForgotPasswordController@find');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Auth', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'auth'], function () {

    Route::post('resetpassword', 'ResetPasswordController@post');
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'dashboard'], function () {
    Route::get('get', 'DashboardController@get');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Dashboard', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'dashboard'], function () {
    Route::post('summary/linechart/get', 'SummaryLineChartController@get');
});

Route::group(['namespace' => 'App\Http\Controllers\API\User', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'profile'], function () {
    Route::get('get', 'ProfileController@get');
    Route::post('update', 'ProfileController@update');

    Route::get('notification/get', 'NotificationController@get');
    Route::post('notification/update', 'NotificationController@update');
});

Route::group(['namespace' => 'App\Http\Controllers\API\User', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'user'], function () {
    Route::post('get', 'UserController@get')->middleware(['scopes:view-user']);
    Route::get('details', 'UserController@details')->middleware(['scopes:view-user']);
    Route::post('create', 'UserController@post')->middleware(['scope:create-user']);
    Route::post('update', 'UserController@update')->middleware(['scope:update-user']);
    Route::post('delete', 'UserController@delete')->middleware(['scope:delete-user']);
    Route::post('export', 'UserController@export')->middleware(['scope:export-user']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'company'], function () {
    Route::post('get', 'CompanyController@get')->middleware(['scope:view-company']);
    Route::get('details', 'CompanyController@details')->middleware(['scope:view-company']);
    Route::post('create', 'CompanyController@post')->middleware(['scope:create-company']);
    Route::post('update', 'CompanyController@update')->middleware(['scope:update-company']);
    Route::post('delete', 'CompanyController@delete')->middleware(['scope:delete-company']);
    Route::post('export', 'CompanyController@export');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Company', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'company/bankaccount'], function () {
    Route::post('get', 'BankAccountController@get');
    Route::get('details', 'BankAccountController@details');
    Route::post('create', 'BankAccountController@post');
    Route::post('update', 'BankAccountController@update');
    Route::post('delete', 'BankAccountController@delete');
    Route::post('export', 'BankAccountController@export');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Company', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'company/bankaccount/balance'], function () {
    Route::post('get', 'BankAccountBalanceController@get');
    Route::get('details', 'BankAccountBalanceController@details');
    Route::post('create', 'BankAccountBalanceController@post');
    Route::post('update', 'BankAccountBalanceController@update');
    Route::post('delete', 'BankAccountBalanceController@delete');
    Route::post('export', 'BankAccountBalanceController@export');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Company', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'company/bankaccount/balance/summary'], function () {
    Route::post('get', 'BalanceSummaryController@get');
});

Route::group(['namespace' => 'App\Http\Controllers\API\Company', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'company/bankaccount/balance/cashflow'], function () {
    Route::post('get', 'CashFlowController@get')->middleware(['scope:view-cashflow']);
    Route::post('export', 'CashFlowController@export')->middleware(['scope:view-cashflow']);
    Route::post('linechart/get', 'CashFlowLineChartController@get')->middleware(['scope:view-cashflow']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'debt'], function () {
    Route::post('get', 'DebtController@get')->middleware(['scope:view-debt']);
    Route::get('details', 'DebtController@details')->middleware(['scope:view-debt']);
    Route::post('create', 'DebtController@post')->middleware(['scope:create-debt']);
    Route::post('update', 'DebtController@update')->middleware(['scope:update-debt']);
    Route::post('delete', 'DebtController@delete')->middleware(['scope:delete-debt']);
    Route::post('export', 'DebtController@export')->middleware(['scope:view-debt']);

    Route::post('events', 'Debt\EventController@get')->middleware(['scope:view-debt']);
    Route::post('summary', 'Debt\SummaryController@get')->middleware(['scope:view-debt']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'rent'], function () {
    Route::post('get', 'RentController@get')->middleware(['scope:view-rent']);
    Route::get('details', 'RentController@details')->middleware(['scope:view-rent']);
    Route::post('create', 'RentController@post')->middleware(['scope:create-rent']);
    Route::post('update', 'RentController@update')->middleware(['scope:update-rent']);
    Route::post('delete', 'RentController@delete')->middleware(['scope:delete-rent']);
    Route::post('export', 'RentController@export')->middleware(['scope:view-rent']);
    //
    Route::post('payment/get', 'Rent\PaymentController@get')->middleware(['scope:view-rent']);
    Route::get('payment/details', 'Rent\PaymentController@details')->middleware(['scope:view-rent']);
    Route::post('payment/create', 'Rent\PaymentController@post')->middleware(['scope:create-rent']);
    Route::post('payment/update', 'Rent\PaymentController@update')->middleware(['scope:update-rent']);
    Route::post('payment/delete', 'Rent\PaymentController@delete')->middleware(['scope:delete-rent']);
    Route::post('payment/export', 'Rent\PaymentController@export')->middleware(['scope:view-rent']);
    //
    Route::post('payment/summary/get', 'Rent\PaymentSummaryController@get')->middleware(['scope:view-rent']);
    Route::post('payment/summary/export', 'Rent\PaymentSummaryController@export')->middleware(['scope:view-rent']);
    Route::post('payment/linechart/get', 'Rent\PaymentLineChartController@get')->middleware(['scope:view-rent']);

    Route::post('arrears/get', 'Rent\ArrearsController@get')->middleware(['scope:view-rent']);
    Route::post('arrears/export', 'Rent\ArrearsController@export')->middleware(['scope:view-rent']);

});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'profit'], function () {
    Route::post('get', 'ProfitController@get')->middleware(['scope:view-profit']);
    Route::post('create', 'ProfitController@post')->middleware(['scope:create-profit']);
    Route::post('update', 'ProfitController@update')->middleware(['scope:update-profit']);
    Route::post('export', 'ProfitController@export')->middleware(['scope:view-profit']);
    Route::post('delete', 'ProfitController@delete')->middleware(['scope:delete-profit']);

    Route::post('company/get', 'Profit\CompanyController@get')->middleware(['scope:view-profit']);
    Route::get('company/details', 'Profit\CompanyController@details')->middleware(['scope:view-profit']);
    Route::post('company/create', 'Profit\CompanyController@post')->middleware(['scope:create-profit']);
    Route::post('company/update', 'Profit\CompanyController@update')->middleware(['scope:update-profit']);
    Route::post('company/delete', 'Profit\CompanyController@delete')->middleware(['scope:delete-profit']);
    Route::post('company/export', 'Profit\CompanyController@export')->middleware(['scope:view-profit']);

    Route::post('summary/get', 'Profit\SummaryController@get')->middleware(['scope:view-profit']);
    Route::post('summary/export', 'Profit\SummaryController@export')->middleware(['scope:view-profit']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'expenses'], function () {
    Route::post('get', 'ExpensesController@get')->middleware(['scope:view-expenses']);
    Route::get('details', 'ExpensesController@details')->middleware(['scope:view-expenses']);
    Route::post('create', 'ExpensesController@post')->middleware(['scope:create-expenses']);
    Route::post('update', 'ExpensesController@update')->middleware(['scope:update-expenses']);
    Route::post('delete', 'ExpensesController@delete')->middleware(['scope:delete-expenses']);
    Route::post('export', 'ExpensesController@export')->middleware(['scope:view-expenses']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'expected/expenses'], function () {
    Route::post('get', 'ExpectedExpensesController@get')->middleware(['scope:view-expected-expenses']);
    Route::get('details', 'ExpectedExpensesController@details')->middleware(['scope:view-expected-expenses']);
    Route::post('create', 'ExpectedExpensesController@post')->middleware(['scope:create-expected-expenses']);
    Route::post('update', 'ExpectedExpensesController@update')->middleware(['scope:update-expected-expenses']);
    Route::post('delete', 'ExpectedExpensesController@delete')->middleware(['scope:delete-expected-expenses']);
    Route::post('export', 'ExpectedExpensesController@export')->middleware(['scope:view-expected-expenses']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'tax'], function () {
    Route::post('get', 'TaxController@get')->middleware(['scope:view-tax']);
    Route::get('details', 'TaxController@details')->middleware(['scope:view-tax']);
    Route::post('create', 'TaxController@post')->middleware(['scope:create-tax']);
    Route::post('update', 'TaxController@update')->middleware(['scope:update-tax']);
    Route::post('delete', 'TaxController@delete')->middleware(['scope:delete-tax']);
    Route::post('export', 'TaxController@export')->middleware(['scope:view-tax']);
});

Route::group(['namespace' => 'App\Http\Controllers\API', 'middleware' => ['auth:api', 'localization'], 'prefix' => 'venue'], function () {
    Route::post('get', 'VenueController@get')->middleware(['scope:view-venue']);
    Route::get('details', 'VenueController@details')->middleware(['scope:view-venue']);
    Route::post('create', 'VenueController@post')->middleware(['scope:create-venue']);
    Route::post('update', 'VenueController@update')->middleware(['scope:update-venue']);
    Route::post('delete', 'VenueController@delete')->middleware(['scope:delete-venue']);
    Route::post('export', 'VenueController@export')->middleware(['scope:view-venue']);

    Route::post('item/get', 'Venue\ItemController@get')->middleware(['scope:view-venue']);
    Route::post('item/create', 'Venue\ItemController@post')->middleware(['scope:create-venue']);
    Route::post('item/update', 'Venue\ItemController@update')->middleware(['scope:update-venue']);
    Route::post('item/delete', 'Venue\ItemController@delete')->middleware(['scope:delete-venue']);
    Route::post('item/export', 'Venue\ItemController@export')->middleware(['scope:view-venue']);

    Route::post('item/amount/get', 'Venue\AmountController@get')->middleware(['scope:view-venue']);
    Route::post('item/amount/create', 'Venue\AmountController@post')->middleware(['scope:create-venue']);
    Route::post('item/amount/update', 'Venue\AmountController@update')->middleware(['scope:update-venue']);
    Route::post('item/amount/delete', 'Venue\AmountController@delete')->middleware(['scope:delete-venue']);
    Route::post('item/amount/export', 'Venue\AmountController@export')->middleware(['scope:view-venue']);

    Route::post('summary/get', 'Venue\SummaryController@get')->middleware(['scope:view-venue']);
    Route::post('summary/export', 'Venue\SummaryController@export')->middleware(['scope:view-venue']);
});