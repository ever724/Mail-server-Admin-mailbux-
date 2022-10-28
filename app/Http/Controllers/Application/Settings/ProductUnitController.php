<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\ProductUnit\Store;
use App\Http\Requests\Application\Settings\ProductUnit\Update;
use App\Interfaces\ProductUnitInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductUnitController extends Controller
{
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
     * Display the Form for Creating New Product Unit.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create product unit');

        return view('application.settings.product.unit.create', [
            'product_unit' => $this->repository->newProductUnit($request),
        ]);
    }

    /**
     * Store the Product Unit in Database.
     *
     * @param \App\Http\Requests\Application\Settings\ProductUnit\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create product unit');

        // Store Product Unit
        $this->repository->createProductUnit($request);

        session()->flash('alert-success', __('messages.product_unit_category_added'));

        return redirect()->route('settings.product', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Product Unit.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update product unit');

        return view('application.settings.product.unit.edit', [
            'product_unit' => $this->repository->getProductUnitById($request, $request->product_unit),
        ]);
    }

    /**
     * Update the Product Unit.
     *
     * @param \App\Http\Requests\Application\Settings\ProductUnit\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update product unit');

        // Update Product Unit
        $this->repository->updateProductUnit($request, $request->product_unit);

        session()->flash('alert-success', __('messages.product_unit_category_updated'));

        return redirect()->route('settings.product', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Product Unit.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete product unit');

        // Delete Product Unit
        if ($this->repository->deleteProductUnit($request, $request->product_unit)) {
            session()->flash('alert-success', __('messages.product_unit_category_deleted'));

            return redirect()->route('settings.product', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
