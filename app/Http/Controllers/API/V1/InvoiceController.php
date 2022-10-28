<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Invoice\Store;
use App\Http\Requests\Application\Invoice\Update;
use App\Http\Resources\InvoiceResource;
use App\Interfaces\InvoiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends BaseController
{
    // Resource
    public $resource = InvoiceResource::class;

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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Gate::authorize('view invoices');

        $invoices = $this->repository->getPaginatedFilteredInvoices($request);

        return $this->sendCollectionResponse($invoices, true, 200);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request)
    {
        Gate::authorize('create invoice');

        // Check if the subscription limit is reached
        if (!$request->currentCompany->subscription('main')->canUseFeature('invoices_per_month')) {
            return $this->sendResponse([], false, 401, [
                'message' => __('messages.you_have_reached_the_limit'),
            ]);
        }

        // Create new Invoice
        $invoice = $this->repository->createInvoice($request);

        return $this->sendResponse($invoice, true, 201, [
            'message' => __('messages.invoice_added'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        Gate::authorize('view invoices');

        $invoice = $this->repository->getInvoiceById($request, $request->invoice);

        return $this->sendResponse($invoice, true, 200);
    }

    /**
     * Send email to customer.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        Gate::authorize('update invoice');

        // Send email to customer
        if ($this->repository->sendInvoiceEmail($request, $request->invoice)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.an_email_sent_to_customer'),
            ]);
        }

        return $this->sendResponse([], false, 500, [
            'message' => session()->get('alert-danger'),
        ]);
    }

    /**
     * Update the status of the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mark(Request $request)
    {
        Gate::authorize('update invoice');

        // Mark the Invoice Status
        $invoice = $this->repository->markInvoiceStatus($request, $request->invoice);

        return $this->sendResponse($invoice, true, 200, [
            'message' => __('messages.invoice_status_updated'),
        ]);
    }

    /**
     * Update the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request)
    {
        Gate::authorize('update invoice');

        // Update Invoice
        $invoice = $this->repository->updateInvoice($request, $request->invoice);

        return $this->sendResponse($invoice, true, 200, [
            'message' => __('messages.invoice_updated'),
        ]);
    }

    /**
     * Delete the specified resource from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        Gate::authorize('delete invoice');

        // Delete invoice
        if ($this->repository->deleteInvoice($request, $request->invoice)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.invoice_deleted'),
            ]);
        }

        return $this->sendResponse(null, false, 500, [
            'message' => session()->get('alert-danger'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
