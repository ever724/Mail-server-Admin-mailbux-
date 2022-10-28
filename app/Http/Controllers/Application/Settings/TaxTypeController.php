<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\TaxType\Store;
use App\Http\Requests\Application\Settings\TaxType\Update;
use App\Interfaces\TaxTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaxTypeController extends Controller
{
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
     * Display Tax Type Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view tax types');

        return view('application.settings.tax_type.index', [
            'tax_types' => $this->repository->getPaginatedFilteredTaxTypes($request),
        ]);
    }

    /**
     * Display the Form for Creating New Tax Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create tax type');

        return view('application.settings.tax_type.create', [
            'tax_type' => $this->repository->newTaxType($request),
        ]);
    }

    /**
     * Store the Tax Type in Database.
     *
     * @param \App\Http\Requests\Application\Settings\TaxType\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create tax type');

        // Store Tax Type
        $this->repository->createTaxType($request);

        session()->flash('alert-success', __('messages.tax_type_added'));

        return redirect()->route('settings.tax_types', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Tax Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update tax type');

        return view('application.settings.tax_type.edit', [
            'tax_type' => $this->repository->getTaxTypeById($request, $request->tax_type),
        ]);
    }

    /**
     * Update the Tax Type.
     *
     * @param \App\Http\Requests\Application\Settings\TaxType\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update tax type');

        // Update Tax Type
        $this->repository->updateTaxType($request, $request->tax_type);

        session()->flash('alert-success', __('messages.tax_type_updated'));

        return redirect()->route('settings.tax_types', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Tax Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete tax type');

        // Delete Tax Type
        if ($this->repository->deleteTaxType($request, $request->tax_type)) {
            session()->flash('alert-success', __('messages.tax_type_deleted'));

            return redirect()->route('settings.tax_types', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
