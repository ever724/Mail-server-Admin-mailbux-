<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Company\Update;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    /**
     * Display Company Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view company settings');

        return view('application.settings.company.index');
    }

    /**
     * Update the Company.
     *
     * @param \App\Http\Requests\Application\Settings\Company\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update company settings');

        $currentCompany = $request->currentCompany;
        $currentCompany->updateModel($request);

        session()->flash('alert-success', __('messages.company_updated'));

        return redirect()->route('settings.company', [
            'company_uid' => $currentCompany->uid,
        ]);
    }
}
