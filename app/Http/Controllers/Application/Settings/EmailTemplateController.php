<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\EmailTemplate\Update;
use Illuminate\Support\Facades\Gate;

class EmailTemplateController extends Controller
{
    /**
     * Display Email Template Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view email templates');

        return view('application.settings.email_template.index');
    }

    /**
     * Update the Email Template Settings.
     *
     * @param \App\Http\Requests\Application\Settings\EmailTemplate\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update email templates');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.email_templates_updated'));

        return redirect()->route('settings.email_template', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
