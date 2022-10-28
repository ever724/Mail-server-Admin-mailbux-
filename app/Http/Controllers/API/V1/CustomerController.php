<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Customer\Store;
use App\Http\Requests\Application\Customer\Update;
use App\Http\Resources\CustomerResource;
use App\Interfaces\CustomerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends BaseController
{
    // Resource
    public $resource = CustomerResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param CustomerInterface $repository
     */
    public function __construct(CustomerInterface $repository)
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
        Gate::authorize('view customers');

        $customers = $this->repository->getPaginatedFilteredCustomers($request);

        return $this->sendCollectionResponse($customers, true, 200);
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
        Gate::authorize('create customer');

        // Check if the subscription limit is reached
        if (!$request->currentCompany->subscription('main')->canUseFeature('customers')) {
            return $this->sendResponse([], false, 401, [
                'message' => __('messages.you_have_reached_the_limit'),
            ]);
        }

        // Store Customer
        $customer = $this->repository->createCustomer($request);

        return $this->sendResponse($customer, true, 201, [
            'message' => __('messages.customer_added'),
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
        Gate::authorize('view customers');

        $customer = $this->repository->getCustomerById($request, $request->customer);

        return $this->sendResponse($customer, true, 200);
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
        Gate::authorize('update customer');

        // Update Customer
        $customer = $this->repository->updateCustomer($request, $request->customer);

        return $this->sendResponse($customer, true, 200, [
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
        Gate::authorize('delete customer');

        // Delete Customer
        if ($this->repository->deleteCustomer($request, $request->customer)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.customer_deleted'),
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
