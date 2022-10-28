<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\ProductUnit\Store;
use App\Http\Requests\Application\Settings\ProductUnit\Update;
use App\Http\Resources\ProductUnitResource;
use App\Interfaces\ProductUnitInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductUnitController extends BaseController
{
    // Resource
    public $resource = ProductUnitResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param ProductUnitInterface $repository
     */
    public function __construct(ProductUnitInterface $repository)
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
        Gate::authorize('view product units');

        $product_units = $this->repository->getPaginatedFilteredProductUnits($request);

        return $this->sendCollectionResponse($product_units, true, 200);
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
        Gate::authorize('create product unit');

        // Store Product Unit
        $product_unit = $this->repository->createProductUnit($request);

        return $this->sendResponse($product_unit, true, 201, [
            'message' => __('messages.product_unit_category_added'),
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
        Gate::authorize('view product units');

        $product_unit = $this->repository->getProductUnitById($request, $request->product_unit);

        return $this->sendResponse($product_unit, true, 200);
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
        Gate::authorize('update product unit');

        // Update Product Unit
        $product_unit = $this->repository->updateProductUnit($request, $request->product_unit);

        return $this->sendResponse($product_unit, true, 200, [
            'message' => __('messages.product_unit_category_updated'),
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
        Gate::authorize('delete product unit');

        // Delete Product Unit
        if ($this->repository->deleteProductUnit($request, $request->product_unit)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.product_unit_category_deleted'),
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
