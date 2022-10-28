<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Notification\Update;

class NotificationController extends Controller
{
    /**
     * Display Notification Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application.settings.notifications.index');
    }

    /**
     * Update the Notification Settings.
     *
     * @param \App\Http\Requests\Application\Settings\Notification\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->user()->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.notification_settings_updated'));

        return redirect()->route('settings.notifications', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
