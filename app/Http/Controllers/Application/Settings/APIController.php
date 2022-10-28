<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class APIController extends Controller
{
    /**
     * Display API Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application.settings.api.index');
    }

    /**
     * Revoke API Access, and redirect to API Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function revoke(Request $request)
    {
        $user = $request->user();
        $user->api_token = hash('sha256', Str::random(60));
        $user->save();

        session()->flash('alert-success', __('messages.api_key_updated'));

        return redirect()->route('settings.api', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
