<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Product\Store;
use App\Http\Requests\Application\Product\Update;
use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
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
     * Display Products Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view products');

        return view('application.products.index', [
            'products' => $this->repository->getPaginatedFilteredProducts($request),
        ]);
    }

    /**
     * Display the Form for Creating New Product.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create product');

        return view('application.products.create', [
            'product' => $this->repository->newProduct($request),
        ]);
    }

    /**
     * Store the Product in Database.
     *
     * @param \App\Http\Requests\Application\Product\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create product');

        // Check if the subscription limit is reached
        /* if (!$request->currentCompany->subscription('main')->canUseFeature('products')) {
            session()->flash('alert-danger', __('messages.you_have_reached_the_limit'));
            return redirect()->back();
        } */

        // Store Product
        $this->repository->createProduct($request);

        session()->flash('alert-success', __('messages.product_added'));

        return redirect()->route('products', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Product.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update product');

        return view('application.products.edit', [
            'product' => $this->repository->getProductById($request, $request->product),
        ]);
    }

    /**
     * Update the Product in Database.
     *
     * @param \App\Http\Requests\Application\Product\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update product');

        // Update Product
        $this->repository->updateProduct($request, $request->product);

        session()->flash('alert-success', __('messages.product_updated'));

        return redirect()->route('products', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Product.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete product');

        // Delete Product
        if ($this->repository->deleteProduct($request, $request->product)) {
            session()->flash('alert-success', __('messages.product_deleted'));

            return redirect()->route('products', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
