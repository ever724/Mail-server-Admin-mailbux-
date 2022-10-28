<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;

class ThemeSettingController extends Controller
{
    /**
     * Display Edit Theme Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        return view('themes.' . $request->theme . '.settings');
    }

    /**
     * Update Theme Settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $theme = $request->theme;

        // Set settings
        foreach ($request->except('_token') as $key => $value) {
            ThemeSetting::setSetting($theme, $key, $value);
        }

        session()->flash('alert-success', __('messages.theme_settings_updated'));

        return redirect()->route('super_admin.settings.theme', $theme);
    }
}
