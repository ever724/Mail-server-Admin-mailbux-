<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Account\Update;

class AccountController extends Controller
{
    /**
     * Display Account Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application.settings.account.index');
    }

    /**
     * Update the Account of Current Authenticated User.
     *
     * @param \App\Http\Requests\Application\Settings\Account\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        $user = $request->user();
        $user->updateModel($request);

        session()->flash('alert-success', __('messages.account_updated'));

        return redirect()->route('settings.account', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
