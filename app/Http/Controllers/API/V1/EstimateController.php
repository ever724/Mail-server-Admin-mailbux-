<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Estimate\Store;
use App\Http\Requests\Application\Estimate\Update;
use App\Http\Resources\EstimateResource;
use App\Http\Resources\InvoiceResource;
use App\Interfaces\EstimateInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EstimateController extends BaseController
{
    // Resource
    public $resource = EstimateResource::class;

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
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Gate::authorize('view estimates');

        $estimates = $this->repository->getPaginatedFilteredEstimates($request);

        return $this->sendCollectionResponse($estimates, true, 200);
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
        Gate::authorize('create estimate');

        // Check if the subscription limit is reached
        if (!$request->currentCompany->subscription('main')->canUseFeature('estimates_per_month')) {
            return $this->sendResponse([], false, 401, [
                'message' => __('messages.you_have_reached_the_limit'),
            ]);
        }

        // Store the estimate
        $estimate = $this->repository->createEstimate($request);

        return $this->sendResponse($estimate, true, 201, [
            'message' => __('messages.estimate_added'),
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
        Gate::authorize('view estimates');

        $estimate = $this->repository->getEstimateById($request, $request->estimate);

        return $this->sendResponse($estimate, true, 200);
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
        Gate::authorize('update estimate');

        // Send email to customer
        if ($this->repository->sendEstimateEmail($request, $request->estimate)) {
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
        Gate::authorize('update estimate');

        // Mark the Estimate Status
        $estimate = $this->repository->markEstimateStatus($request, $request->estimate);

        return $this->sendResponse($estimate, true, 200, [
            'message' => __('messages.estimate_status_updated'),
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
        Gate::authorize('update estimate');

        // Update Estimate
        $estimate = $this->repository->updateEstimate($request, $request->estimate);

        return $this->sendResponse($estimate, true, 200, [
            'message' => __('messages.estimate_updated'),
        ]);
    }

    /**
     * Convert the specified resource to an invoice.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(Request $request)
    {
        Gate::authorize('create invoice');

        // Convert estimate to invoice
        $invoice = $this->repository->convertEstimateToInvoice($request, $request->estimate);

        // Check if the invoice is created
        if ($invoice) {
            return (new InvoiceResource($invoice))
                ->additional(['success' => true, 'message' => __('messages.invoice_added')])
                ->response()
                ->setStatusCode(201);
        }

        return $this->sendResponse([], false, 500, [
            'message' => session()->get('alert-danger'),
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
        Gate::authorize('delete estimate');

        // Delete Estimate
        if ($this->repository->deleteEstimate($request, $request->estimate)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.estimate_deleted'),
            ]);
        }

        return $this->sendResponse([], false, 500, [
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
