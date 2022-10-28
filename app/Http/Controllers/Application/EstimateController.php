<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Estimate\Store;
use App\Http\Requests\Application\Estimate\Update;
use App\Interfaces\EstimateInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EstimateController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param EstimateInterface $repository
     */
    public function __construct(EstimateInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Estimates Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view estimates');

        return view('application.estimates.index', [
            'estimates' => $this->repository->getPaginatedFilteredEstimates($request),
            'tab' => $request->route('tab', 'drafts'),
        ]);
    }

    /**
     * Display the Form for Creating New Estimate.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create estimate');

        $estimate = $this->repository->newEstimate($request);

        return view('application.estimates.create', [
            'estimate' => $estimate,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => (bool) $request->currentCompany->getSetting('tax_per_item'),
            'discount_per_item' => (bool) $request->currentCompany->getSetting('discount_per_item'),
        ]);
    }

    /**
     * Store the Estimate in Database.
     *
     * @param \App\Http\Requests\Application\Estimate\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create estimate');

        // Check if the subscription limit is reached
        /* if (!$request->currentCompany->subscription('main')->canUseFeature('estimates_per_month')) {
            session()->flash('alert-danger', __('messages.you_have_reached_the_limit'));
            return redirect()->back();
        } */

        // Store the estimate
        $estimate = $this->repository->createEstimate($request);

        session()->flash('alert-success', __('messages.estimate_added'));

        return redirect()->route('estimates.details', [
            'estimate' => $estimate->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Estimate Details Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        Gate::authorize('view estimates');

        return view('application.estimates.details', [
            'estimate' => $this->repository->getEstimateById($request, $request->estimate),
        ]);
    }

    /**
     * Send an email to customer about the Estimate.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        Gate::authorize('update estimate');

        // Send email to customer
        if ($this->repository->sendEstimateEmail($request, $request->estimate)) {
            session()->flash('alert-success', __('messages.an_email_sent_to_customer'));
        }

        return redirect()->route('estimates.details', [
            'estimate' => $request->estimate,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Change Status of the Estimate by Given Status.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mark(Request $request)
    {
        Gate::authorize('update estimate');

        // Mark the Estimate Status
        $estimate = $this->repository->markEstimateStatus($request, $request->estimate);

        session()->flash('alert-success', __('messages.estimate_status_updated'));

        return redirect()->route('estimates.details', [
            'estimate' => $estimate->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Estimate.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update estimate');

        $estimate = $this->repository->getEstimateById($request, $request->estimate);

        return view('application.estimates.edit', [
            'estimate' => $estimate,
            'customers' => $request->currentCompany->customers,
            'products' => $request->currentCompany->products,
            'tax_per_item' => (bool) $estimate->tax_per_item,
            'discount_per_item' => (bool) $estimate->discount_per_item,
        ]);
    }

    /**
     * Update the Estimate in Database.
     *
     * @param \App\Http\Requests\Application\Estimate\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update estimate');

        // Update Estimate
        $estimate = $this->repository->updateEstimate($request, $request->estimate);

        session()->flash('alert-success', __('messages.estimate_updated'));

        return redirect()->route('estimates.details', [
            'estimate' => $estimate->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Convert the Estimate to an Invoice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function convert(Request $request)
    {
        Gate::authorize('create invoice');

        // Convert estimate to invoice
        $invoice = $this->repository->convertEstimateToInvoice($request, $request->estimate);

        // Check if the invoice is created
        if ($invoice) {
            session()->flash('alert-success', __('messages.invoice_added'));

            return redirect()->route('invoices.details', [
                'invoice' => $invoice->id,
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete the Estimate.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete estimate');

        // Delete Estimate
        if ($this->repository->deleteEstimate($request, $request->estimate)) {
            session()->flash('alert-success', __('messages.estimate_deleted'));

            return redirect()->route('estimates', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
