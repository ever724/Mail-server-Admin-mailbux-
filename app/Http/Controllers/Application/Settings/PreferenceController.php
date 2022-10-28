<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Preference\Update;
use Illuminate\Support\Facades\Gate;

class PreferenceController extends Controller
{
    /**
     * Display Preferences Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view preferences');

        return view('application.settings.preference.index');
    }

    /**
     * Update the Preferences.
     *
     * @param \App\Http\Requests\Application\Settings\Preference\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update preferences');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.preferences_updated'));

        return redirect()->route('settings.preferences', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
