<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\CustomField\Store;
use App\Http\Requests\Application\Settings\CustomField\Update;
use App\Interfaces\CustomFieldInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomFieldController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param CustomFieldInterface $repository
     */
    public function __construct(CustomFieldInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Custom Field Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view custom fields');

        return view('application.settings.custom_field.index', [
            'custom_fields' => $this->repository->getPaginatedFilteredCustomFields($request),
        ]);
    }

    /**
     * Display the Form for Creating New Custom Field.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create custom field');

        return view('application.settings.custom_field.create', [
            'custom_field' => $this->repository->newCustomField($request),
        ]);
    }

    /**
     * Store the Custom Field in Database.
     *
     * @param \App\Http\Requests\Application\Settings\CustomField\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create custom field');

        // Store Custom Field
        $this->repository->createCustomField($request);

        session()->flash('alert-success', __('messages.custom_field_created'));

        return redirect()->route('settings.custom_fields', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Custom Field.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update custom field');

        return view('application.settings.custom_field.edit', [
            'custom_field' => $this->repository->getCustomFieldById($request, $request->custom_field),
        ]);
    }

    /**
     * Update the Custom Field.
     *
     * @param \App\Http\Requests\Application\Settings\CustomField\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update custom field');

        // Update Custom Field
        $this->repository->updateCustomField($request, $request->custom_field);

        session()->flash('alert-success', __('messages.custom_field_updated'));

        return redirect()->route('settings.custom_fields', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Custom Field.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete custom field');

        // Delete Custom Field
        if ($this->repository->deleteCustomField($request, $request->custom_field)) {
            session()->flash('alert-success', __('messages.custom_field_deleted'));

            return redirect()->route('settings.custom_fields', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
