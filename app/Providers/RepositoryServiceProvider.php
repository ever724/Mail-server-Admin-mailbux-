<?php

namespace App\Providers;

use App\Interfaces\CreditNoteInterface;
use App\Interfaces\CreditNoteRefundInterface;
use App\Interfaces\CustomerInterface;
use App\Interfaces\CustomFieldInterface;
use App\Interfaces\EstimateInterface;
use App\Interfaces\ExpenseCategoryInterface;
use App\Interfaces\ExpenseInterface;
use App\Interfaces\InvoiceInterface;
use App\Interfaces\PaymentInterface;
use App\Interfaces\PaymentTypeInterface;
use App\Interfaces\PlanInterface;
use App\Interfaces\ProductInterface;
use App\Interfaces\ProductUnitInterface;
use App\Interfaces\SupportTicketInterface;
use App\Interfaces\TaxTypeInterface;
use App\Interfaces\TeamMemberInterface;
use App\Interfaces\VendorInterface;
use App\Repositories\CreditNoteRefundRepository;
use App\Repositories\CreditNoteRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EstimateRepository;
use App\Repositories\ExpenseCategoryRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentTypeRepository;
use App\Repositories\PlanRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductUnitRepository;
use App\Repositories\SupportTicketRepository;
use App\Repositories\TaxTypeRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\VendorRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(CreditNoteInterface::class, CreditNoteRepository::class);
        $this->app->bind(CreditNoteRefundInterface::class, CreditNoteRefundRepository::class);
        $this->app->bind(CustomerInterface::class, CustomerRepository::class);
        $this->app->bind(PlanInterface::class, PlanRepository::class);
        $this->app->bind(CustomFieldInterface::class, CustomFieldRepository::class);
        $this->app->bind(EstimateInterface::class, EstimateRepository::class);
        $this->app->bind(ExpenseCategoryInterface::class, ExpenseCategoryRepository::class);
        $this->app->bind(ExpenseInterface::class, ExpenseRepository::class);
        $this->app->bind(InvoiceInterface::class, InvoiceRepository::class);
        $this->app->bind(PaymentInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentTypeInterface::class, PaymentTypeRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(ProductUnitInterface::class, ProductUnitRepository::class);
        $this->app->bind(SupportTicketInterface::class, SupportTicketRepository::class);
        $this->app->bind(TaxTypeInterface::class, TaxTypeRepository::class);
        $this->app->bind(TeamMemberInterface::class, TeamMemberRepository::class);
        $this->app->bind(VendorInterface::class, VendorRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
