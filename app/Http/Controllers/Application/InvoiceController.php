<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Invoice\Store;
use App\Http\Requests\Application\Invoice\Update;
use App\Interfaces\InvoiceInterface;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class InvoiceController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param InvoiceInterface $repository
     */
    public function __construct(InvoiceInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Invoices Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view invoices');

        return view('application.invoices.index', [
            'invoices' => $this->repository->getPaginatedFilteredInvoices($request),
            'tab' => $request->route('tab', 'drafts'),
        ]);
    }

    /**
     * Display the Form for Creating New Invoice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create invoice');

        $invoice = $this->repository->newInvoice($request);

        return view('application.invoices.create', [
            'invoice' => $invoice,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => (bool) $request->currentCompany->getSetting('tax_per_item'),
            'discount_per_item' => (bool) $request->currentCompany->getSetting('discount_per_item'),
        ]);
    }

    /**
     * Store the Invoice in Database.
     *
     * @param \App\Http\Requests\Application\Invoice\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create invoice');

        // Check if the subscription limit is reached
        /*  if (!$request->currentCompany->subscription('main')->canUseFeature('invoices_per_month')) {
            session()->flash('alert-danger', __('messages.you_have_reached_the_limit'));
            return redirect()->back();
        } */

        // Create new Invoice
        $invoice = $this->repository->createInvoice($request);

        session()->flash('alert-success', __('messages.invoice_added'));

        return redirect()->route('invoices.details', [
            'invoice' => $invoice->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Invoice Details Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        Gate::authorize('view invoices');

        $invoice = $this->repository->getInvoiceById($request, $request->invoice);

        return view('application.invoices.details', [
            'invoice' => $invoice,
            'payments' => $invoice->payments()->orderBy('payment_number')->paginate(50),
            'activities' => Activity::where('causer_id', $invoice->id)->get(),
        ]);
    }

    /**
     * Send an email to customer about the Invoice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        Gate::authorize('update invoice');

        // Send email to customer
        if ($this->repository->sendInvoiceEmail($request, $request->invoice)) {
            session()->flash('alert-success', __('messages.an_email_sent_to_customer'));
        }

        return redirect()->route('invoices.details', [
            'invoice' => $request->invoice,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Change Status of the Invoice by Given Status.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mark(Request $request)
    {
        Gate::authorize('update invoice');

        // Mark the Invoice Status
        $invoice = $this->repository->markInvoiceStatus($request, $request->invoice);

        session()->flash('alert-success', __('messages.invoice_status_updated'));

        return redirect()->route('invoices.details', [
            'invoice' => $invoice->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Invoice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update invoice');

        $invoice = $this->repository->getInvoiceById($request, $request->invoice);

        return view('application.invoices.edit', [
            'invoice' => $invoice,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => $invoice->tax_per_item,
            'discount_per_item' => $invoice->discount_per_item,
        ]);
    }

    /**
     * Update the Invoice in Database.
     *
     * @param \App\Http\Requests\Application\Invoice\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update invoice');

        // Update Invoice
        $invoice = $this->repository->updateInvoice($request, $request->invoice);

        session()->flash('alert-success', __('messages.invoice_updated'));

        return redirect()->route('invoices.details', [
            'invoice' => $invoice->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Invoice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete invoice');

        // Delete invoice
        if ($this->repository->deleteInvoice($request, $request->invoice)) {
            session()->flash('alert-success', __('messages.invoice_deleted'));

            return redirect()->route('invoices', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
