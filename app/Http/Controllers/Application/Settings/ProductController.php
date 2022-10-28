<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Product\Update;
use App\Interfaces\ProductUnitInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
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
     * Display Product Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('application.settings.product.index', [
            'product_units' => $this->repository->getPaginatedFilteredProductUnits($request),
        ]);
    }

    /**
     * Update the Product Settings.
     *
     * @param \App\Http\Requests\Application\Settings\Product\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update product settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.product_settings_updated'));

        return redirect()->route('settings.product', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
