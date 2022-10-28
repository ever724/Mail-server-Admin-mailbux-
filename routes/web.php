<?php

use App\Http\Controllers\Mailbux\OrderController;
use App\Http\Controllers\SuperAdmin\ClientController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\SubscriptionInvoiceController;
use App\Http\Controllers\SuperAdmin\SupportTicketController;
use App\Http\Middleware\SubscriptionBelongsToMailbuxClient;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

//-----------------------------------------//
//             INSTALLER ROUTES            //
//-----------------------------------------//
Route::group(['namespace' => 'Installer'], function () {
    Route::get('/install', 'InstallController@welcome')->name('installer.welcome');
    Route::get('/install/requirements', 'InstallController@requirements')->name('installer.requirements');
    Route::get('/install/permissions', 'InstallController@permissions')->name('installer.permissions');
    Route::get('/install/environment', 'InstallController@environment')->name('installer.environment');
    Route::post('/install/environment/save', 'InstallController@save_environment')->name('installer.environment.save');
    Route::get('/install/database', 'InstallController@database')->name('installer.database');
    Route::get('/install/final', 'InstallController@finish')->name('installer.final');

    // Updated
    Route::get('/update', 'UpdateController@welcome')->name('updater.welcome');
    Route::get('/update/overview', 'UpdateController@overview')->name('updater.overview');
    Route::get('/update/database', 'UpdateController@database')->name('updater.database');
    Route::get('/update/final', 'UpdateController@finish')->name('updater.final');
});

// Static Pages
Route::get('/pages/{slug}', 'PageController@index')->name('pages');

// Landing
Route::get('/', 'HomeController@index')->name('home');
Route::get('/demo', 'HomeController@demo')->name('demo');
Route::get('/change-language/{locale}', 'HomeController@change_language')->name('change_language');

// Auth routes
Route::middleware(ProtectAgainstSpam::class)->group(function () {
    Auth::routes([
        'register' => false,
        'verify' => true,
    ]);
});

// PDF Views
Route::get('/viewer/invoice/{invoice}/pdf', 'Application\PDFController@invoice')->name('pdf.invoice');
Route::get('/viewer/credit_note/{credit_note}/pdf', 'Application\PDFController@credit_note')->name('pdf.credit_note');
Route::get('/viewer/estimate/{estimate}/pdf', 'Application\PDFController@estimate')->name('pdf.estimate');
Route::get('/viewer/payment/{payment}/pdf', 'Application\PDFController@payment')->name('pdf.payment');

// Webhooks
Route::post('/order/checkout/{plan}/mollie/webhook', 'Application\OrderController@mollie_webhook')->name('order.payment.mollie.webhook');
Route::post('/portal/{customer}/invoices/{invoice}/mollie/webhook', 'Checkout\MollieController@webhook')->name('customer_portal.invoices.mollie.webhook');

// Super Admin Panel
Route::group(['namespace' => 'SuperAdmin', 'prefix' => '/admin', 'middleware' => ['auth', 'super_admin']], function () {
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('super_admin.dashboard');

    // Users
    Route::get('/users', 'UserController@index')->name('super_admin.users');
    Route::get('/users/create', 'UserController@create')->name('super_admin.users.create');
    Route::post('/users/store', 'UserController@store')->middleware('blocked_at_demo')->name('super_admin.users.store');
    Route::get('/users/{user}/edit', 'UserController@edit')->name('super_admin.users.edit');
    Route::post('/users/{user}/edit', 'UserController@update')->middleware('blocked_at_demo')->name('super_admin.users.update');
    Route::get('/users/{user}/delete', 'UserController@delete')->middleware('blocked_at_demo')->name('super_admin.users.delete');
    Route::get('/users/{user}/impersonate', 'UserController@impersonate')->name('super_admin.users.impersonate');

    // Plans
    Route::get('/plans', 'PlanController@index')->name('super_admin.plans');
    Route::get('/plans/create', 'PlanController@create')->name('super_admin.plans.create');
    Route::post('/plans/store', 'PlanController@store')->middleware('blocked_at_demo')->name('super_admin.plans.store');
    Route::get('/plans/{plan}/edit', 'PlanController@edit')->name('super_admin.plans.edit');
    Route::post('/plans/{plan}/edit', [PlanController::class, 'update'])->middleware('blocked_at_demo')->name('super_admin.plans.update');
    Route::get('/plans/{plan}/delete', 'PlanController@delete')->middleware('blocked_at_demo')->name('super_admin.plans.delete');

    // Pages
    Route::get('/pages', 'PageController@index')->name('super_admin.pages');
    Route::get('/pages/create', 'PageController@create')->name('super_admin.pages.create');
    Route::post('/pages/store', 'PageController@store')->middleware('blocked_at_demo')->name('super_admin.pages.store');
    Route::get('/pages/{page}/edit', 'PageController@edit')->name('super_admin.pages.edit');
    Route::post('/pages/{page}/edit', 'PageController@update')->middleware('blocked_at_demo')->name('super_admin.pages.update');
    Route::get('/pages/{page}/delete', 'PageController@delete')->middleware('blocked_at_demo')->name('super_admin.pages.delete');

    // Subscriptions
    Route::get('/subscriptions', 'SubscriptionController@index')->name('super_admin.subscriptions');
    Route::get('/subscriptions/{subscription}/cancel', 'SubscriptionController@cancel')->middleware('blocked_at_demo')->name('super_admin.subscriptions.cancel');

    // Orders
    Route::get('/orders', 'OrderController@index')->name('super_admin.orders');

    // Languages
    Route::get('/languages', 'LanguageController@index')->name('super_admin.languages');
    Route::get('/languages/create', 'LanguageController@create')->name('super_admin.languages.create');
    Route::post('/languages/create', 'LanguageController@store')->middleware('blocked_at_demo')->name('super_admin.languages.store');
    Route::get('/languages/{language}/default', 'LanguageController@set_default')->middleware('blocked_at_demo')->name('super_admin.languages.set_default');
    Route::get('/languages/{language}/translations', 'LanguageTranslationController@index')->name('super_admin.languages.translations');
    Route::post('/languages/{language}', 'LanguageTranslationController@update')->name('super_admin.languages.translations.update');

    // Settings
    Route::get('/settings/application', 'SettingController@application')->name('super_admin.settings.application');
    Route::post('/settings/application', 'SettingController@application_update')->middleware('blocked_at_demo')->name('super_admin.settings.application.update');
    Route::get('/settings/application/client-token-refresh', [SettingController::class, 'refreshClientKey'])->middleware('blocked_at_demo')->name('super_admin.settings.application.client-token-refresh');

    Route::get('/settings/mail', 'SettingController@mail')->name('super_admin.settings.mail');
    Route::post('/settings/mail', 'SettingController@mail_update')->middleware('blocked_at_demo')->name('super_admin.settings.mail.update');

    Route::get('/settings/mailbux-server', 'SettingController@mailbuxserver')->name('super_admin.settings.mailbuxserver');
    Route::post('/settings/mailbux-server', 'SettingController@mailbuxserver_update')->middleware('blocked_at_demo')->name('super_admin.settings.mailbuxserver.update');
    Route::get('/settings/mailbux-server/refresh', [SettingController::class, 'refreshMailbuxToken'])->middleware('blocked_at_demo')->name('super_admin.settings.mailbuxserver.refresh-token');

    Route::get('/settings/payment', 'SettingController@payment')->name('super_admin.settings.payment');
    Route::post('/settings/payment', 'SettingController@payment_update')->middleware('blocked_at_demo')->name('super_admin.settings.payment.update');

    Route::get('/settings/custom-css-js', 'SettingController@custom_css_js')->name('super_admin.settings.custom_css_js');
    Route::post('/settings/custom-css-js', 'SettingController@custom_css_js_update')->middleware('blocked_at_demo')->name('super_admin.settings.custom_css_js.update');

    Route::get('/settings/theme/{theme}', 'ThemeSettingController@edit')->name('super_admin.settings.theme');
    Route::post('/settings/theme/{theme}', 'ThemeSettingController@update')->middleware('blocked_at_demo')->name('super_admin.settings.theme.update');
    Route::get('/settings/theme/{theme}/activate', 'ThemeSettingController@activate')->middleware('blocked_at_demo')->name('super_admin.settings.theme.activate');

    Route::prefix('invoices')->group(
        function () {
            Route::get('/', [SubscriptionInvoiceController::class, 'index'])->name('super_admin.invoices');
        }
    );

    Route::prefix('clients')->group(
        function () {
            Route::get('/', [ClientController::class, 'index'])->name('super_admin.clients');
            Route::get('sync', [ClientController::class, 'sync'])->name('super_admin.clients.sync');
            Route::get('create', [ClientController::class, 'create'])->name('super_admin.clients.create');
            Route::post('store', [ClientController::class, 'store'])->name('super_admin.clients.store');

            Route::prefix('{client}')->group(function () {
                Route::get('delete', [ClientController::class, 'delete'])->name('super_admin.clients.delete');
                Route::get('edit', [ClientController::class, 'edit'])->name('super_admin.clients.edit');
                Route::post('update', [ClientController::class, 'update'])->name('super_admin.clients.update');
            });
        }
    );

    Route::prefix('support-tickets')
        ->group(function () {
            Route::get('/', [SupportTicketController::class, 'index'])->name('super_admin.support_tickets');
            Route::get('/downloads/{attachment}', [SupportTicketController::class, 'downloadAttachment'])
                ->name('super_admin.support_tickets.download-attachment');
            Route::prefix('{support_ticket}')
                ->group(function () {
                    Route::get('/', [SupportTicketController::class, 'show'])->name('super_admin.support_tickets.show');
                    Route::post('/', [SupportTicketController::class, 'reply'])->name('super_admin.support_tickets.reply');
                    Route::post('close', [SupportTicketController::class, 'close'])->name('super_admin.support_tickets.close');
                });

            Route::bind('support_ticket', function ($id) {
                return SupportTicket::query()->findOrFail($id);
            });
        });
});

Route::prefix('payment')->group(function () {
    Route::prefix('{client}')->group(function () {
        Route::get('/{plan_id}', [OrderController::class, 'index'])->name('mailbux.payment.start');

        Route::prefix('subscriptions')->group(
            function () {
                Route::prefix('{subscription}')
                    ->middleware([SubscriptionBelongsToMailbuxClient::class])
                    ->group(
                        function () {
                            Route::get('update', [OrderController::class, 'update'])->name('mailbux.payment.update');
                            Route::get('update/success', [OrderController::class, 'afterUpdate'])->name('mailbux.payment.update.success');
                            Route::get('cancel', [OrderController::class, 'cancel'])->name('payment.subscription.cancel');
                            Route::get('cancel/success', [OrderController::class, 'afterCancel'])->name('payment.subscription.cancel.success');
                        }
                    );
            }
        );

        Route::prefix('/invoices/{invoice}')
            ->middleware([SubscriptionBelongsToMailbuxClient::class])
            ->group(function () {
                Route::get('/', [SubscriptionInvoiceController::class, 'html'])->name('payment.invoices.html');
                Route::get('/pdf', [SubscriptionInvoiceController::class, 'pdf'])->name('payment.invoices.pdf');
                Route::get('/download', [SubscriptionInvoiceController::class, 'downloadPdf'])->name('payment.invoices.download');
            });
    });

    Route::get('complete', [OrderController::class, 'paddle_completed'])->name('mailbux.payment.complete');
});

// Customer Portal Routes
Route::group(['namespace' => 'CustomerPortal', 'prefix' => '/portal/{customer}', 'middleware' => ['customer_portal']], function () {
    // Authentication
    Route::get('/auth/login', 'AuthController@login')->name('customer_portal.login');
    Route::post('/auth/login', 'AuthController@login_submit')->name('customer_portal.login.submit');
    Route::get('/auth/forgot-password', 'AuthController@forgot_password')->name('customer_portal.forgot_password');
    Route::post('/auth/forgot-password', 'AuthController@forgot_password_submit')->name('customer_portal.forgot_password.submit');
    Route::get('/auth/reset-password/{token}', 'AuthController@reset_password')->name('customer_portal.reset_password');
    Route::post('/auth/reset-password/{token}', 'AuthController@reset_password_submit')->name('customer_portal.reset_password.submit');
    Route::get('/auth/logout', 'AuthController@logout')->name('customer_portal.auth.logout');

    Route::group(['middleware' => ['customer_portal_auth']], function () {
        // Dashboard
        Route::get('/', 'DashboardController@index');
        Route::get('/dashboard', 'DashboardController@index')->name('customer_portal.dashboard');

        // Invoices
        Route::get('/invoices', 'InvoiceController@index')->name('customer_portal.invoices');
        Route::get('/invoices/{invoice}', 'InvoiceController@show')->name('customer_portal.invoices.details');

        // PaypalExpress Checkout
        Route::post('/invoices/{invoice}/paypal/payment', 'Checkout\PaypalExpressController@payment')->name('customer_portal.invoices.paypal.payment');
        Route::get('/invoices/{invoice}/paypal/completed', 'Checkout\PaypalExpressController@completed')->name('customer_portal.invoices.paypal.completed');
        Route::get('/invoices/{invoice}/paypal/cancelled', 'Checkout\PaypalExpressController@cancelled')->name('customer_portal.invoices.paypal.cancelled');

        // Mollie Checkout
        Route::get('/invoices/{invoice}/mollie/payment', 'Checkout\MollieController@payment')->name('customer_portal.invoices.mollie.payment');
        Route::get('/invoices/{invoice}/mollie/completed', 'Checkout\MollieController@completed')->name('customer_portal.invoices.mollie.completed');

        // Razorpay Checkout
        Route::get('/invoices/{invoice}/razorpay/checkout', 'Checkout\RazorpayController@checkout')->name('customer_portal.invoices.razorpay.checkout');
        Route::post('/invoices/{invoice}/razorpay/callback', 'Checkout\RazorpayController@callback')->name('customer_portal.invoices.razorpay.callback');

        // Stripe Checkout
        Route::get('/invoices/{invoice}/stripe/checkout', 'Checkout\StripeController@checkout')->name('customer_portal.invoices.stripe.checkout');
        Route::post('/invoices/{invoice}/stripe/payment', 'Checkout\StripeController@payment')->name('customer_portal.invoices.stripe.payment');
        Route::get('/invoices/{invoice}/stripe/completed', 'Checkout\StripeController@completed')->name('customer_portal.invoices.stripe.completed');

        // Credit Notes
        Route::get('/credit-notes', 'CreditNoteController@index')->name('customer_portal.credit_notes');
        Route::get('/credit-notes/{credit_note}', 'CreditNoteController@show')->name('customer_portal.credit_notes.details');

        // Estimates
        Route::get('/estimates', 'EstimateController@index')->name('customer_portal.estimates');
        Route::get('/estimates/{estimate}', 'EstimateController@show')->name('customer_portal.estimates.details');
        Route::get('/estimates/{estimate}/mark/{status?}', 'EstimateController@mark')->name('customer_portal.estimates.mark');

        // Payment
        Route::get('/payments', 'PaymentController@index')->name('customer_portal.payments');
        Route::get('/payments/{payment}', 'PaymentController@show')->name('customer_portal.payments.details');
    });
});

// Application Routes
Route::group(['namespace' => 'Application', 'prefix' => '/{company_uid}', 'middleware' => ['auth', 'dashboard', 'verified']], function () {
    // Company Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Customers
    Route::get('/customers', 'CustomerController@index')->name('customers');
    Route::get('/customers/create', 'CustomerController@create')->name('customers.create');
    Route::post('/customers/create', 'CustomerController@store')->name('customers.store');
    Route::get('/customers/{customer}/details', 'CustomerController@details')->name('customers.details');
    Route::get('/customers/{customer}/edit', 'CustomerController@edit')->name('customers.edit');
    Route::post('/customers/{customer}/edit', 'CustomerController@update')->middleware('blocked_at_demo')->name('customers.update');
    Route::get('/customers/{customer}/delete', 'CustomerController@delete')->middleware('blocked_at_demo')->name('customers.delete');

    // Products & Services
    Route::get('/products', 'ProductController@index')->name('products');
    Route::get('/products/create', 'ProductController@create')->name('products.create');
    Route::post('/products/create', 'ProductController@store')->name('products.store');
    Route::get('/products/{product}/edit', 'ProductController@edit')->name('products.edit');
    Route::post('/products/{product}/edit', 'ProductController@update')->name('products.update');
    Route::get('/products/{product}/delete', 'ProductController@delete')->name('products.delete');

    // Invoices
    Route::get('/invoices/create', 'InvoiceController@create')->name('invoices.create');
    Route::post('/invoices/create', 'InvoiceController@store')->name('invoices.store');
    Route::get('/invoices/{invoice}/details', 'InvoiceController@show')->name('invoices.details');
    Route::get('/invoices/{invoice}/edit', 'InvoiceController@edit')->name('invoices.edit');
    Route::post('/invoices/{invoice}/edit', 'InvoiceController@update')->name('invoices.update');
    Route::get('/invoices/{invoice}/delete', 'InvoiceController@delete')->name('invoices.delete');
    Route::get('/invoices/{invoice}/send', 'InvoiceController@send')->name('invoices.send');
    Route::get('/invoices/{invoice}/mark/{status?}', 'InvoiceController@mark')->name('invoices.mark');
    Route::get('/invoices/{tab?}', 'InvoiceController@index')->name('invoices');

    // Credit Notes
    Route::get('/credit-notes', 'CreditNoteController@index')->name('credit_notes');
    Route::get('/credit-notes/create', 'CreditNoteController@create')->name('credit_notes.create');
    Route::post('/credit-notes/create', 'CreditNoteController@store')->name('credit_notes.store');
    Route::get('/credit-notes/{credit_note}/details', 'CreditNoteController@show')->name('credit_notes.details');
    Route::get('/credit-notes/{credit_note}/edit', 'CreditNoteController@edit')->name('credit_notes.edit');
    Route::post('/credit-notes/{credit_note}/edit', 'CreditNoteController@update')->name('credit_notes.update');
    Route::get('/credit-notes/{credit_note}/delete', 'CreditNoteController@delete')->name('credit_notes.delete');
    Route::get('/credit-notes/{credit_note}/send', 'CreditNoteController@send')->name('credit_notes.send');
    Route::get('/credit-notes/{credit_note}/mark/{status?}', 'CreditNoteController@mark')->name('credit_notes.mark');
    Route::get('/credit-notes/{credit_note}/refund', 'CreditNoteRefundController@create')->name('credit_notes.refund');
    Route::post('/credit-notes/{credit_note}/refund', 'CreditNoteRefundController@store')->name('credit_notes.refund.store');
    Route::get('/credit-notes/{credit_note}/refund/{refund}/delete', 'CreditNoteRefundController@delete')->name('credit_notes.refund.delete');

    // Estimates
    Route::get('/estimates/create', 'EstimateController@create')->name('estimates.create');
    Route::post('/estimates/create', 'EstimateController@store')->name('estimates.store');
    Route::get('/estimates/{estimate}/details', 'EstimateController@show')->name('estimates.details');
    Route::get('/estimates/{estimate}/edit', 'EstimateController@edit')->name('estimates.edit');
    Route::post('/estimates/{estimate}/edit', 'EstimateController@update')->name('estimates.update');
    Route::get('/estimates/{estimate}/delete', 'EstimateController@delete')->name('estimates.delete');
    Route::get('/estimates/{estimate}/send', 'EstimateController@send')->name('estimates.send');
    Route::get('/estimates/{estimate}/convert', 'EstimateController@convert')->name('estimates.convert');
    Route::get('/estimates/{estimate}/mark/{status?}', 'EstimateController@mark')->name('estimates.mark');
    Route::get('/estimates/{tab?}', 'EstimateController@index')->name('estimates');

    // Payments
    Route::get('/payments', 'PaymentController@index')->name('payments');
    Route::get('/payments/create', 'PaymentController@create')->name('payments.create');
    Route::post('/payments/create', 'PaymentController@store')->name('payments.store');
    Route::get('/payments/{payment}/edit', 'PaymentController@edit')->name('payments.edit');
    Route::post('/payments/{payment}/edit', 'PaymentController@update')->name('payments.update');
    Route::get('/payments/{payment}/delete', 'PaymentController@delete')->name('payments.delete');

    // Expenses
    Route::get('/expenses', 'ExpenseController@index')->name('expenses');
    Route::get('/expenses/create', 'ExpenseController@create')->name('expenses.create');
    Route::post('/expenses/create', 'ExpenseController@store')->name('expenses.store');
    Route::get('/expenses/{expense}/edit', 'ExpenseController@edit')->name('expenses.edit');
    Route::post('/expenses/{expense}/edit', 'ExpenseController@update')->name('expenses.update');
    Route::get('/expenses/{expense}/receipt', 'ExpenseController@download_receipt')->name('expenses.download_receipt');
    Route::get('/expenses/{expense}/delete', 'ExpenseController@delete')->name('expenses.delete');

    // Vendors
    Route::get('/vendors', 'VendorController@index')->name('vendors');
    Route::get('/vendors/create', 'VendorController@create')->name('vendors.create');
    Route::post('/vendors/create', 'VendorController@store')->name('vendors.store');
    Route::get('/vendors/{vendor}/details', 'VendorController@details')->name('vendors.details');
    Route::get('/vendors/{vendor}/edit', 'VendorController@edit')->name('vendors.edit');
    Route::post('/vendors/{vendor}/edit', 'VendorController@update')->name('vendors.update');
    Route::get('/vendors/{vendor}/delete', 'VendorController@delete')->name('vendors.delete');

    // Reports
    Route::get('/reports/customer-sales', 'ReportController@customer_sales')->name('reports.customer_sales');
    Route::get('/reports/customer-sales/pdf', 'PDFReportController@customer_sales')->name('reports.customer_sales.pdf');
    Route::get('/reports/product-sales', 'ReportController@product_sales')->name('reports.product_sales');
    Route::get('/reports/product-sales/pdf', 'PDFReportController@product_sales')->name('reports.product_sales.pdf');
    Route::get('/reports/profit-loss', 'ReportController@profit_loss')->name('reports.profit_loss');
    Route::get('/reports/profit-loss/pdf', 'PDFReportController@profit_loss')->name('reports.profit_loss.pdf');
    Route::get('/reports/expenses', 'ReportController@expenses')->name('reports.expenses');
    Route::get('/reports/expenses/pdf', 'PDFReportController@expenses')->name('reports.expenses.pdf');
    Route::get('/reports/vendors', 'ReportController@vendors')->name('reports.vendors');
    Route::get('/reports/vendors/pdf', 'PDFReportController@vendors')->name('reports.vendors.pdf');

    // Setting Routes
    Route::group(['namespace' => 'Settings', 'prefix' => 'settings'], function () {
        // Settings>Account Settings
        Route::get('/account', 'AccountController@index')->name('settings.account');
        Route::post('/account', 'AccountController@update')->middleware('blocked_at_demo')->name('settings.account.update');

        // Settings>Account Settings
        Route::get('/membership', 'MembershipController@index')->name('settings.membership');
        Route::get('/membership/{order_id}/invoice', 'MembershipController@order_invoice')->name('settings.membership.invoice');

        // Settings>Notification Settings
        Route::get('/notifications', 'NotificationController@index')->name('settings.notifications');
        Route::post('/notifications', 'NotificationController@update')->name('settings.notifications.update');

        // Settings>Company Settings
        Route::get('/company', 'CompanyController@index')->name('settings.company');
        Route::post('/company', 'CompanyController@update')->middleware('blocked_at_demo')->name('settings.company.update');

        // Settings>Preferences
        Route::get('/preferences', 'PreferenceController@index')->name('settings.preferences');
        Route::post('/preferences', 'PreferenceController@update')->name('settings.preferences.update');

        // Settings>Invoice Settings
        Route::get('/invoice', 'InvoiceController@index')->name('settings.invoice');
        Route::post('/invoice', 'InvoiceController@update')->name('settings.invoice.update');

        // Settings>Estimate Settings
        Route::get('/estimate', 'EstimateController@index')->name('settings.estimate');
        Route::post('/estimate', 'EstimateController@update')->name('settings.estimate.update');

        // Settings>Payment Settings
        Route::get('/payment', 'PaymentController@index')->name('settings.payment');
        Route::post('/payment', 'PaymentController@update')->name('settings.payment.update');
        Route::get('/payment/type/create', 'PaymentTypeController@create')->name('settings.payment.type.create');
        Route::post('/payment/type/create', 'PaymentTypeController@store')->name('settings.payment.type.store');
        Route::get('/payment/type/{type}/edit', 'PaymentTypeController@edit')->name('settings.payment.type.edit');
        Route::post('/payment/type/{type}/edit', 'PaymentTypeController@update')->name('settings.payment.type.update');
        Route::get('/payment/type/{type}/delete', 'PaymentTypeController@delete')->name('settings.payment.type.delete');
        Route::get('/payment/gateway/{gateway}/edit', 'PaymentGatewayController@edit')->name('settings.payment.gateway.edit');
        Route::post('/payment/gateway/{gateway}/edit', 'PaymentGatewayController@update')->middleware('blocked_at_demo')->name('settings.payment.gateway.update');

        // Settings>Product Settings
        Route::get('/product', 'ProductController@index')->name('settings.product');
        Route::post('/product', 'ProductController@update')->name('settings.product.update');
        Route::get('/product/unit/create', 'ProductUnitController@create')->name('settings.product.unit.create');
        Route::post('/product/unit/create', 'ProductUnitController@store')->name('settings.product.unit.store');
        Route::get('/product/unit/{product_unit}/edit', 'ProductUnitController@edit')->name('settings.product.unit.edit');
        Route::post('/product/unit/{product_unit}/edit', 'ProductUnitController@update')->name('settings.product.unit.update');
        Route::get('/product/unit/{product_unit}/delete', 'ProductUnitController@delete')->name('settings.product.unit.delete');

        // Settings>Tax Types
        Route::get('/tax-types', 'TaxTypeController@index')->name('settings.tax_types');
        Route::get('/tax-types/create', 'TaxTypeController@create')->name('settings.tax_types.create');
        Route::post('/tax-types/create', 'TaxTypeController@store')->name('settings.tax_types.store');
        Route::get('/tax-types/{tax_type}/edit', 'TaxTypeController@edit')->name('settings.tax_types.edit');
        Route::post('/tax-types/{tax_type}/edit', 'TaxTypeController@update')->name('settings.tax_types.update');
        Route::get('/tax-types/{tax_type}/delete', 'TaxTypeController@delete')->name('settings.tax_types.delete');

        // Settings>Custom Fields
        Route::get('/custom-fields', 'CustomFieldController@index')->name('settings.custom_fields');
        Route::get('/custom-fields/create', 'CustomFieldController@create')->name('settings.custom_fields.create');
        Route::post('/custom-fields/create', 'CustomFieldController@store')->name('settings.custom_fields.store');
        Route::get('/custom-fields/{custom_field}/edit', 'CustomFieldController@edit')->name('settings.custom_fields.edit');
        Route::post('/custom-fields/{custom_field}/edit', 'CustomFieldController@update')->name('settings.custom_fields.update');
        Route::get('/custom-fields/{custom_field}/delete', 'CustomFieldController@delete')->name('settings.custom_fields.delete');

        // Settings>Expense Categories
        Route::get('/expense-categories', 'ExpenseCategoryController@index')->name('settings.expense_categories');
        Route::get('/expense-categories/create', 'ExpenseCategoryController@create')->name('settings.expense_categories.create');
        Route::post('/expense-categories/create', 'ExpenseCategoryController@store')->name('settings.expense_categories.store');
        Route::get('/expense-categories/{expense_category}/edit', 'ExpenseCategoryController@edit')->name('settings.expense_categories.edit');
        Route::post('/expense-categories/{expense_category}/edit', 'ExpenseCategoryController@update')->name('settings.expense_categories.update');
        Route::get('/expense-categories/{expense_category}/delete', 'ExpenseCategoryController@delete')->name('settings.expense_categories.delete');

        // Settings>Team
        Route::get('/team', 'TeamMemberController@index')->name('settings.team');
        Route::get('/team/add-member', 'TeamMemberController@createMember')->name('settings.team.createMember');
        Route::post('/team/add-member', 'TeamMemberController@storeMember')->middleware('blocked_at_demo')->name('settings.team.storeMember');
        Route::get('/team/{member}/edit', 'TeamMemberController@editMember')->name('settings.team.editMember');
        Route::post('/team/{member}/edit', 'TeamMemberController@updateMember')->middleware('blocked_at_demo')->name('settings.team.updateMember');
        Route::get('/team/{member}/delete', 'TeamMemberController@deleteMember')->middleware('blocked_at_demo')->name('settings.team.deleteMember');

        // Settings>Email Templates
        Route::get('/email-templates', 'EmailTemplateController@index')->name('settings.email_template');
        Route::post('/email-templates', 'EmailTemplateController@update')->middleware('blocked_at_demo')->name('settings.email_template.update');

        // Settings>API Credentials
        Route::get('/api', 'APIController@index')->name('settings.api');
        Route::get('/api/revoke', 'APIController@revoke')->middleware('blocked_at_demo')->name('settings.api.revoke');
    });

    // Ajax requests
    Route::get('/ajax/products', 'AjaxController@products')->name('ajax.products');
    Route::get('/ajax/customers', 'AjaxController@customers')->name('ajax.customers');
    Route::get('/ajax/invoices', 'AjaxController@invoices')->name('ajax.invoices');
});

// Order & Checkout Routes
Route::group(['namespace' => 'Application', 'middleware' => ['auth', 'dashboard', 'verified']], function () {
    // Orders
    Route::get('/order/plans', 'OrderController@plans')->name('order.plans');
    Route::get('/order/checkout/{plan}', 'OrderController@checkout')->name('order.checkout');
    Route::get('/order/processing', 'OrderController@order_processing')->name('order.processing');

    // PaypalExpress Checkout
    Route::post('/order/checkout/{plan}/paypal/payment', 'OrderController@paypal')->name('order.payment.paypal');
    Route::get('/order/checkout/{plan}/paypal/completed', 'OrderController@paypal_completed')->name('order.payment.paypal.completed');
    Route::get('/order/checkout/{plan}/paypal/cancelled', 'OrderController@paypal_cancelled')->name('order.payment.paypal.cancelled');

    // Mollie Checkout
    Route::get('/order/checkout/{plan}/mollie/payment', 'OrderController@mollie')->name('order.payment.mollie');
    Route::get('/order/checkout/{plan}/mollie/completed', 'OrderController@mollie_completed')->name('order.payment.mollie.completed');

    // Razorpay Checkout
    Route::post('/order/checkout/{plan}/razorpay', 'OrderController@razorpay')->name('order.payment.razorpay');

    // Stripe Checkout
    Route::post('/order/checkout/{plan}/stripe', 'OrderController@stripe')->name('order.payment.stripe');
    Route::get('/order/checkout/{plan}/stripe/completed', 'OrderController@stripe_completed')->name('order.payment.stripe.completed');
});
