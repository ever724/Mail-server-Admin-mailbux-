<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Vendor\Store;
use App\Http\Requests\Application\Vendor\Update;
use App\Http\Resources\VendorResource;
use App\Interfaces\VendorInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorController extends BaseController
{
    // Resource
    public $resource = VendorResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param VendorInterface $repository
     */
    public function __construct(VendorInterface $repository)
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
        Gate::authorize('view vendors');

        $vendors = $this->repository->getPaginatedFilteredVendors($request);

        return $this->sendCollectionResponse($vendors, true, 200);
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
        Gate::authorize('create vendor');

        // Store Vendor
        $vendor = $this->repository->createVendor($request);

        return $this->sendResponse($vendor, true, 201, [
            'message' => __('messages.vendor_added'),
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
        Gate::authorize('view vendors');

        $vendor = $this->repository->getVendorById($request, $request->vendor);

        return $this->sendResponse($vendor, true, 200);
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
        Gate::authorize('update vendor');

        // Update Vendor
        $vendor = $this->repository->updateVendor($request, $request->vendor);

        return $this->sendResponse($vendor, true, 200, [
            'message' => __('messages.vendor_updated'),
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
        Gate::authorize('delete vendor');

        // Delete Vendor
        if ($this->repository->deleteVendor($request, $request->vendor)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.vendor_deleted'),
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
