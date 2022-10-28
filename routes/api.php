<?php

use App\Http\Controllers\API\V1\ClientController;
use App\Http\Controllers\API\V1\ConfigController;
use App\Http\Controllers\API\V1\PlanController;
use App\Http\Controllers\API\V1\PlanSubscriptionController;
use App\Http\Controllers\API\V1\SupportTicketController;
use App\Http\Controllers\SuperAdmin\SupportTicketController as SuperAdminSupportTicketController;
use App\Http\Controllers\Webhooks\AutoDeployController;
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
Route::post('/webhooks/deploy', [AutoDeployController::class, 'deploy']);

// V1 API Routes
Route::group(['namespace' => 'API\V1', 'prefix' => 'v1', 'middleware' => ['auth:api', 'api_request']], function () {
    // Account Settings
    Route::get('/account/current', 'AccountController@current');
    Route::put('/account/settings', 'AccountController@settings');
    Route::put('/account/notification', 'AccountController@notification');

    // Company Settings
    Route::put('/company/settings', 'CompanyController@settings');
    Route::put('/company/preferences', 'CompanyController@preferences');
    Route::put('/company/invoice', 'CompanyController@invoice');
    Route::put('/company/estimate', 'CompanyController@estimate');
    Route::put('/company/payment', 'CompanyController@payment');
    Route::put('/company/product', 'CompanyController@product');
    Route::put('/company/email-templates', 'CompanyController@email_templates');

    // Customers
    Route::get('/customers', 'CustomerController@index');
    Route::post('/customers', 'CustomerController@store');
    Route::get('/customers/{customer}', 'CustomerController@show');
    Route::match(['post', 'patch', 'put'], '/customers/{customer}', 'CustomerController@update');
    Route::delete('/customers/{customer}', 'CustomerController@delete');

    // Products
    Route::get('/products', 'ProductController@index');
    Route::post('/products', 'ProductController@store');
    Route::get('/products/{product}', 'ProductController@show');
    Route::match(['post', 'patch', 'put'], '/products/{product}', 'ProductController@update');
    Route::delete('/products/{product}', 'ProductController@delete');

    // Invoices
    Route::get('/invoices', 'InvoiceController@index');
    Route::post('/invoices', 'InvoiceController@store');
    Route::get('/invoices/{invoice}', 'InvoiceController@show');
    Route::match(['post', 'patch', 'put'], '/invoices/{invoice}', 'InvoiceController@update');
    Route::delete('/invoices/{invoice}', 'InvoiceController@delete');
    Route::put('/invoices/{invoice}/send', 'InvoiceController@send');
    Route::match(['post', 'patch', 'put'], '/invoices/{invoice}/mark', 'InvoiceController@mark');

    // Credit Notes
    Route::get('/credit-notes', 'CreditNoteController@index');
    Route::post('/credit-notes', 'CreditNoteController@store');
    Route::get('/credit-notes/{credit_note}', 'CreditNoteController@show');
    Route::match(['post', 'patch', 'put'], '/credit-notes/{credit_note}', 'CreditNoteController@update');
    Route::delete('/credit-notes/{credit_note}', 'CreditNoteController@delete');
    Route::put('/credit-notes/{credit_note}/send', 'CreditNoteController@send');
    Route::match(['post', 'patch', 'put'], '/credit-notes/{credit_note}/mark', 'CreditNoteController@mark');

    // Credit Note refunds
    Route::get('/credit-notes/{credit_note}/refunds', 'CreditNoteRefundController@index');
    Route::post('/credit-notes/{credit_note}/refunds', 'CreditNoteRefundController@store');
    Route::delete('/credit-notes/{credit_note}/refunds/{refund}', 'CreditNoteRefundController@destroy');

    // Estimates
    Route::get('/estimates', 'EstimateController@index');
    Route::post('/estimates', 'EstimateController@store');
    Route::get('/estimates/{estimate}', 'EstimateController@show');
    Route::match(['post', 'patch', 'put'], '/estimates/{estimate}', 'EstimateController@update');
    Route::delete('/estimates/{estimate}', 'EstimateController@delete');
    Route::put('/estimates/{estimate}/send', 'EstimateController@send');
    Route::put('/estimates/{estimate}/mark', 'EstimateController@mark');
    Route::post('/estimates/{estimate}/convert', 'EstimateController@convert');

    // Payments
    Route::get('/payments', 'PaymentController@index');
    Route::post('/payments', 'PaymentController@store');
    Route::get('/payments/{payment}', 'PaymentController@show');
    Route::match(['post', 'patch', 'put'], '/payments/{payment}', 'PaymentController@update');
    Route::delete('/payments/{payment}', 'PaymentController@delete');

    // Expenses
    Route::get('/expenses', 'ExpenseController@index');
    Route::post('/expenses', 'ExpenseController@store');
    Route::get('/expenses/{expense}', 'ExpenseController@show');
    Route::match(['post', 'patch', 'put'], '/expenses/{expense}', 'ExpenseController@update');
    Route::delete('/expenses/{expense}', 'ExpenseController@delete');

    // Vendors
    Route::get('/vendors', 'VendorController@index');
    Route::post('/vendors', 'VendorController@store');
    Route::get('/vendors/{vendor}', 'VendorController@show');
    Route::match(['post', 'patch', 'put'], '/vendors/{vendor}', 'VendorController@update');
    Route::delete('/vendors/{vendor}', 'VendorController@delete');

    // Custom Fields
    Route::get('/custom-fields', 'CustomFieldController@index');
    Route::post('/custom-fields', 'CustomFieldController@store');
    Route::get('/custom-fields/{custom_field}', 'CustomFieldController@show');
    Route::match(['post', 'patch', 'put'], '/custom-fields/{custom_field}', 'CustomFieldController@update');
    Route::delete('/custom-fields/{custom_field}', 'CustomFieldController@delete');

    // Expense Categories
    Route::get('/expense-categories', 'ExpenseCategoryController@index');
    Route::post('/expense-categories', 'ExpenseCategoryController@store');
    Route::get('/expense-categories/{expense_category}', 'ExpenseCategoryController@show');
    Route::match(['post', 'patch', 'put'], '/expense-categories/{expense_category}', 'ExpenseCategoryController@update');
    Route::delete('/expense-categories/{expense_category}', 'ExpenseCategoryController@delete');

    // Payment Types
    Route::get('/payment-types', 'PaymentTypeController@index');
    Route::post('/payment-types', 'PaymentTypeController@store');
    Route::get('/payment-types/{payment_type}', 'PaymentTypeController@show');
    Route::match(['post', 'patch', 'put'], '/payment-types/{payment_type}', 'PaymentTypeController@update');
    Route::delete('/payment-types/{payment_type}', 'PaymentTypeController@delete');

    // Product Units
    Route::get('/product-units', 'ProductUnitController@index');
    Route::post('/product-units', 'ProductUnitController@store');
    Route::get('/product-units/{product_unit}', 'ProductUnitController@show');
    Route::match(['post', 'patch', 'put'], '/product-units/{product_unit}', 'ProductUnitController@update');
    Route::delete('/product-units/{product_unit}', 'ProductUnitController@delete');

    // Tax Types
    Route::get('/tax-types', 'TaxTypeController@index');
    Route::post('/tax-types', 'TaxTypeController@store');
    Route::get('/tax-types/{tax_type}', 'TaxTypeController@show');
    Route::match(['post', 'patch', 'put'], '/tax-types/{tax_type}', 'TaxTypeController@update');
    Route::delete('/tax-types/{tax_type}', 'TaxTypeController@delete');

    // Team Members
    Route::get('/team-members', 'TeamMemberController@index');
    Route::post('/team-members', 'TeamMemberController@store');
    Route::get('/team-members/{team_member}', 'TeamMemberController@show');
    Route::match(['post', 'patch', 'put'], '/team-members/{team_member}', 'TeamMemberController@update');
    Route::delete('/team-members/{team_member}', 'TeamMemberController@delete');
    Route::apiResource('/team-members', 'TeamMemberController');

    // Currencies
    Route::get('currencies', 'CurrencyController@index');

    // Countries
    Route::get('countries', 'CountryController@index');
});

Route::prefix('v1')
    ->namespace('API\V1')
    ->middleware(['client-request'])
    ->group(function () {
        Route::middleware(['client-logged-in'])
            ->group(function () {
                Route::get('plans', [PlanController::class, 'index']);

                Route::prefix('support-tickets')
                    ->group(function () {
                        Route::post('/', [SupportTicketController::class, 'store']);
                        Route::post('query', [SupportTicketController::class, 'getFiltered']);

                        Route::prefix('{support_ticket}')
                            ->middleware(['client-user-access'])
                            ->group(function () {
                                Route::get('/', [SupportTicketController::class, 'show'])->name('api.support_tickets.show');
                                Route::post('reply', [SupportTicketController::class, 'reply']);
                                Route::get('download/{attachment}', [SuperAdminSupportTicketController::class, 'downloadAttachment'])
                                    ->name('api.support-tickets.download-attachment');
                            });
                    });

                Route::prefix('subscriptions')
                    ->group(function () {
                        Route::get('/', [PlanSubscriptionController::class, 'index']);

                        Route::prefix('{plan_subscription}')
                            ->middleware(['client-user-access'])
                            ->group(function () {
                                Route::get('/', [PlanSubscriptionController::class, 'show']);
                            });
                    });
            });

        Route::prefix('clients')->group(
            function () {
                Route::get('sync', [ClientController::class, 'sync']);
            }
        );

        Route::get('config/{key}', [ConfigController::class, 'getConfigValue']);
    });
