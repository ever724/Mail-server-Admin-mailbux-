<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Estimate\Update;
use Illuminate\Support\Facades\Gate;

class EstimateController extends Controller
{
    /**
     * Display Estimate Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view estimate settings');

        return view('application.settings.estimate.index');
    }

    /**
     * Update the Estimate Settings.
     *
     * @param \App\Http\Requests\Application\Settings\Estimate\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update estimate settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.estimate_settings_updated'));

        return redirect()->route('settings.estimate', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
