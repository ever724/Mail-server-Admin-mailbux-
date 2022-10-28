<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Console\Commands\CreateMailClientApiToken;
use App\Console\Commands\UpdateMailbuxServerToken;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display Edit Application Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function application()
    {
        return view('super_admin.settings.application');
    }

    /**
     * Update Application Settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|RedirectResponse
     */
    public function application_update(Request $request)
    {
        // Validate
        $request->validate([
            'application_name' => 'required|string|max:140',
            'application_currency' => 'required|string',
            'meta_description' => 'required|string|max:200',
            'meta_keywords' => 'required|string|max:200',
            'google_recapthca_key' => 'nullable|string',
            'expiring_subscription_due_mail_subject' => 'required|string',
            'expiring_subscription_due_mail_content' => 'required|string',
            'expiring_subscription_due_before_x_days' => 'nullable|integer',
            'expiring_subscription_overdue_mail_subject' => 'required|string',
            'expiring_subscription_overdue_mail_content' => 'required|string',
            'expiring_subscription_overdue_after_x_days' => 'nullable|integer',
        ]);

        // Update settings
        SystemSetting::setSetting('application_name', $request->application_name);
        SystemSetting::setSetting('application_currency', $request->application_currency);
        SystemSetting::setSetting('meta_description', $request->meta_description);
        SystemSetting::setSetting('meta_keywords', $request->meta_keywords);
        SystemSetting::setSetting('google_recapthca_key', $request->google_recapthca_key);
        SystemSetting::setSetting('expiring_subscription_due_mail_subject', $request->expiring_subscription_due_mail_subject);
        SystemSetting::setSetting('expiring_subscription_due_mail_content', $request->expiring_subscription_due_mail_content);
        SystemSetting::setSetting('expiring_subscription_due_before_x_days', $request->expiring_subscription_due_before_x_days);
        SystemSetting::setSetting('expiring_subscription_overdue_mail_subject', $request->expiring_subscription_overdue_mail_subject);
        SystemSetting::setSetting('expiring_subscription_overdue_mail_content', $request->expiring_subscription_overdue_mail_content);
        SystemSetting::setSetting('expiring_subscription_overdue_after_x_days', $request->expiring_subscription_overdue_after_x_days);

        // Update favicon
        if ($request->favicon) {
            $request->validate(['favicon' => 'required|mimes:png,jpg,ico|max:2048']);
            $path = $request->favicon->storeAs('favicons', 'favicon.' . $request->favicon->getClientOriginalExtension(), 'public_dir');
            SystemSetting::setSetting('application_favicon', asset('/uploads/' . $path));
        }

        // Update logo
        if ($request->logo) {
            $request->validate(['logo' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->logo->storeAs('logo', 'logo.' . $request->logo->getClientOriginalExtension(), 'public_dir');
            SystemSetting::setSetting('application_logo', asset('/uploads/' . $path));
        }

        // Update Google recaptcha keys
        $env = [
            'GOOGLE_CAPTCHA_SITE_KEY' => $request->google_recapthca_key,
            'GOOGLE_CAPTCHA_PRIVATE_KEY' => $request->google_recapthca_secret_key,
        ];
        SystemSetting::setEnvironmentValue($env);

        session()->flash('alert-success', __('messages.application_settings_updated'));

        return redirect()->route('super_admin.settings.application');
    }

    /**
     * Display Edit Mail Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function mail()
    {
        return view('super_admin.settings.mail');
    }

    /**
     * Update Mail Settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|RedirectResponse
     */
    public function mail_update(Request $request)
    {
        // Validate
        $request->validate([
            'mail_driver' => 'required|string|max:50',
            'mail_host' => 'required|string|max:50',
            'mail_port' => 'required|string|max:50',
            'mail_username' => 'nullable|string|max:50',
            'mail_password' => 'nullable|string|max:50',
            'mail_from_address' => 'required|string|max:50',
            'mail_from_name' => 'required|string|max:50',
            'mail_encryption' => 'required|string|max:50',
        ]);

        $env = [
            'MAIL_DRIVER' => $request->mail_driver,
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_PASSWORD' => $request->mail_password,
            'MAIL_ENCRYPTION' => $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name,
        ];

        // Update settings
        if (SystemSetting::setEnvironmentValue($env)) {
            session()->flash('alert-success', __('messages.mail_settings_updated'));
        } else {
            session()->flash('alert-danger', __('messages.something_went_wrong'));
        }

        return redirect()->route('super_admin.settings.mail');
    }

    /**
     * Display Edit Payment Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function mailbuxserver()
    {
        return view('super_admin.settings.mailbuxserver');
    }

    /**
     * Update Payment Settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|RedirectResponse
     */
    public function mailbuxserver_update(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'mailbux_domain_url' => 'required|url|max:191',
            'mailbux_username' => 'required|string|max:100',
            'mailbux_password' => 'required|string|max:100',
            'mailbux_active' => 'required|boolean',
        ]);

        // Update each settings
        foreach ($validated as $key => $value) {
            SystemSetting::setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.mailbuxserver_settings_updated'));

        return redirect()->route('super_admin.settings.mailbuxserver');
    }

    public function refreshMailbuxToken(): RedirectResponse
    {
        Artisan::call(UpdateMailbuxServerToken::class);

        session()->flash('alert-success', __('messages.mailbuxserver_settings_updated'));

        return redirect()->route('super_admin.settings.mailbuxserver');
    }

    /**
     * Display Edit Payment Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        return view('super_admin.settings.payment');
    }

    /**
     * Update Payment Settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|RedirectResponse
     */
    public function payment_update(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'paddle_vendor_id' => 'nullable|string',
            'paddle_vendor_auth_code' => 'nullable|string',
            'paddle_sandbox' => 'nullable|boolean',
            'paddle_active' => 'nullable|boolean',
            'stripe_public_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'stripe_test_mode' => 'nullable|boolean',
            'stripe_active' => 'nullable|boolean',
            'paypal_username' => 'nullable|string',
            'paypal_password' => 'nullable|string',
            'paypal_signature' => 'nullable|string',
            'paypal_test_mode' => 'nullable|boolean',
            'paypal_active' => 'nullable|boolean',
            'razorpay_id' => 'nullable|string',
            'razorpay_secret_key' => 'nullable|string',
            'razorpay_test_mode' => 'nullable|boolean',
            'razorpay_active' => 'nullable|boolean',
            'mollie_api_key' => 'nullable|string',
            'mollie_test_mode' => 'nullable|boolean',
            'mollie_active' => 'nullable|boolean',
        ]);

        $env = [
            'PADDLE_VENDOR_ID' => $request->paddle_vendor_id,
            'PADDLE_VENDOR_AUTH_CODE' => $request->paddle_vendor_auth_code,
            'PADDLE_SANDBOX' => $request->paddle_sandbox,
        ];

        // Update each settings
        foreach ($validated as $key => $value) {
            SystemSetting::setSetting($key, $value);
        }
        SystemSetting::setEnvironmentValue($env);

        session()->flash('alert-success', __('messages.payment_settings_updated'));

        return redirect()->route('super_admin.settings.payment');
    }

    /**
     * Display Cron Configuration Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function cron()
    {
        return view('super_admin.settings.cron');
    }

    /**
     * Display Custom CSS/JS Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function custom_css_js()
    {
        $custom_css = Storage::disk('public_dir')->exists('/custom/custom.css') ? Storage::disk('public_dir')->get('/custom/custom.css') : '';
        $custom_js = Storage::disk('public_dir')->exists('/custom/custom.js') ? Storage::disk('public_dir')->get('/custom/custom.js') : '';
        $tracking_code = Storage::disk('public_dir')->exists('/custom/tracking.txt') ? Storage::disk('public_dir')->get('/custom/tracking.txt') : '';

        return view('super_admin.settings.custom_css_js', [
            'custom_css' => $custom_css,
            'custom_js' => $custom_js,
            'tracking_code' => $tracking_code,
        ]);
    }

    /**
     * Update Custom CSS/JS Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function custom_css_js_update(Request $request)
    {
        // Store customs in file
        Storage::disk('public_dir')->put('/custom/custom.css', $request->custom_css);
        Storage::disk('public_dir')->put('/custom/custom.js', $request->custom_js);
        Storage::disk('public_dir')->put('/custom/tracking.txt', $request->tracking_code);

        session()->flash('alert-success', __('messages.custom_css_js_updated'));

        return redirect()->route('super_admin.settings.custom_css_js');
    }

    /**
     * @return RedirectResponse
     */
    public function refreshClientKey(): RedirectResponse
    {
        Artisan::call(CreateMailClientApiToken::class);

        session()->flash('alert-success', __('messages.token_updated'));

        return redirect()->route('super_admin.settings.application');
    }
}
