<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\TaxType\Store;
use App\Http\Requests\Application\Settings\TaxType\Update;
use App\Http\Resources\TaxTypeResource;
use App\Interfaces\TaxTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaxTypeController extends BaseController
{
    // Resource
    public $resource = TaxTypeResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param TaxTypeInterface $repository
     */
    public function __construct(TaxTypeInterface $repository)
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
        Gate::authorize('view tax types');

        $tax_types = $this->repository->getPaginatedFilteredTaxTypes($request);

        return $this->sendCollectionResponse($tax_types, true, 200);
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
        Gate::authorize('create tax type');

        // Store Tax Type
        $tax_type = $this->repository->createTaxType($request);

        return $this->sendResponse($tax_type, true, 201, [
            'message' => __('messages.tax_type_added'),
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
        Gate::authorize('view tax types');

        $tax_type = $this->repository->getTaxTypeById($request, $request->tax_type);

        return $this->sendResponse($tax_type, true, 200);
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
        Gate::authorize('update tax type');

        // Update Tax Type
        $tax_type = $this->repository->updateTaxType($request, $request->tax_type);

        return $this->sendResponse($tax_type, true, 200, [
            'message' => __('messages.tax_type_updated'),
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
        Gate::authorize('delete tax type');

        // Delete Tax Type
        if ($this->repository->deleteTaxType($request, $request->tax_type)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.tax_type_deleted'),
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
