<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Invoice\Update;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    /**
     * Display Invoice Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view invoice settings');

        return view('application.settings.invoice.index');
    }

    /**
     * Update the Invoice Settings.
     *
     * @param \App\Http\Requests\Application\Settings\Invoice\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update invoice settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.invoice_settings_updated'));

        return redirect()->route('settings.invoice', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
