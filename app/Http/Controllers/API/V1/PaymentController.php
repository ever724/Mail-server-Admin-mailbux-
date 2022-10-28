<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Payment\Store;
use App\Http\Requests\Application\Payment\Update;
use App\Http\Resources\PaymentResource;
use App\Interfaces\PaymentInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends BaseController
{
    // Resource
    public $resource = PaymentResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param PaymentInterface $repository
     */
    public function __construct(PaymentInterface $repository)
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
        Gate::authorize('view payments');

        $payments = $this->repository->getPaginatedFilteredPayments($request);

        return $this->sendCollectionResponse($payments, true, 200);
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
        Gate::authorize('create payment');

        // Store Payment
        $payment = $this->repository->createPayment($request);

        return $this->sendResponse($payment, true, 201, [
            'message' => __('messages.payment_added'),
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
        Gate::authorize('view payments');

        $payment = $this->repository->getPaymentById($request, $request->payment);

        return $this->sendResponse($payment, true, 200);
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
        Gate::authorize('update payment');

        // Update payment
        $product = $this->repository->updatePayment($request, $request->payment);

        return $this->sendResponse($product, true, 200, [
            'message' => __('messages.payment_updated'),
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
        Gate::authorize('delete payment');

        // Delete payment
        if ($this->repository->deletePayment($request, $request->payment)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.payment_deleted'),
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
