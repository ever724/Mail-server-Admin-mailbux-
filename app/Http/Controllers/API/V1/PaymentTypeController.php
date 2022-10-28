<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\PaymentType\Store;
use App\Http\Requests\Application\Settings\PaymentType\Update;
use App\Http\Resources\PaymentTypeResource;
use App\Interfaces\PaymentTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentTypeController extends BaseController
{
    // Resource
    public $resource = PaymentTypeResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param PaymentTypeInterface $repository
     */
    public function __construct(PaymentTypeInterface $repository)
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
        Gate::authorize('view payment types');

        $payment_types = $this->repository->getPaginatedFilteredPaymentTypes($request);

        return $this->sendCollectionResponse($payment_types, true, 200);
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
        Gate::authorize('create payment type');

        // Store Payment Type
        $payment_type = $this->repository->createPaymentType($request);

        return $this->sendResponse($payment_type, true, 201, [
            'message' => __('messages.payment_type_category_added'),
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
        Gate::authorize('view payment types');

        $payment_type = $this->repository->getPaymentTypeById($request, $request->payment_type);

        return $this->sendResponse($payment_type, true, 200);
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
        Gate::authorize('update payment type');

        // Update Payment Type
        $payment_type = $this->repository->updatePaymentType($request, $request->payment_type);

        return $this->sendResponse($payment_type, true, 200, [
            'message' => __('messages.payment_type_category_updated'),
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
        Gate::authorize('delete payment type');

        // Delete Payment Type
        if ($this->repository->deletePaymentType($request, $request->payment_type)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.payment_type_category_deleted'),
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
