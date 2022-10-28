<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Product\Store;
use App\Http\Requests\Application\Product\Update;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends BaseController
{
    // Resource
    public $resource = ProductResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param ProductInterface $repository
     */
    public function __construct(ProductInterface $repository)
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
        Gate::authorize('view products');

        $products = $this->repository->getPaginatedFilteredProducts($request);

        return $this->sendCollectionResponse($products, true, 200);
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
        Gate::authorize('create product');

        // Check if the subscription limit is reached
        if (!$request->currentCompany->subscription('main')->canUseFeature('products')) {
            return $this->sendResponse([], false, 401, [
                'message' => __('messages.you_have_reached_the_limit'),
            ]);
        }

        // Store Product
        $product = $this->repository->createProduct($request);

        return $this->sendResponse($product, true, 201, [
            'message' => __('messages.product_added'),
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
        Gate::authorize('view products');

        $product = $this->repository->getProductById($request, $request->product);

        return $this->sendResponse($product, true, 200);
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
        Gate::authorize('update product');

        // Update Product
        $product = $this->repository->updateProduct($request, $request->product);

        return $this->sendResponse($product, true, 200, [
            'message' => __('messages.product_updated'),
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
        Gate::authorize('delete product');

        // Delete Product
        if ($this->repository->deleteProduct($request, $request->product)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.product_deleted'),
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
