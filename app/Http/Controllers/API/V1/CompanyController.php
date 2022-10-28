<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\Company\Update as CompanyUpdate;
use App\Http\Requests\Application\Settings\EmailTemplate\Update as EmailTemplateUpdate;
use App\Http\Requests\Application\Settings\Estimate\Update as EstimateUpdate;
use App\Http\Requests\Application\Settings\Invoice\Update as InvoiceUpdate;
use App\Http\Requests\Application\Settings\Payment\Update as PaymentUpdate;
use App\Http\Requests\Application\Settings\Preference\Update as PreferenceUpdate;
use App\Http\Requests\Application\Settings\Product\Update as ProductUpdate;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Gate;

class CompanyController extends BaseController
{
    // Resource
    public $resource = CompanyResource::class;

    /**
     * Update the company's settings.
     *
     * @param CompanyUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function settings(CompanyUpdate $request)
    {
        Gate::authorize('update company settings');

        $currentCompany = $request->currentCompany;
        $currentCompany->updateModel($request);

        return $this->sendResponse($currentCompany, true, 200, [
            'message' => __('messages.company_updated'),
        ]);
    }

    /**
     * Update the company's preferences.
     *
     * @param PreferenceUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function preferences(PreferenceUpdate $request)
    {
        Gate::authorize('update preferences');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.preferences_updated'),
        ]);
    }

    /**
     * Update the company's invoice settings.
     *
     * @param InvoiceUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoice(InvoiceUpdate $request)
    {
        Gate::authorize('update invoice settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.invoice_settings_updated'),
        ]);
    }

    /**
     * Update the company's estimate settings.
     *
     * @param EstimateUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function estimate(EstimateUpdate $request)
    {
        Gate::authorize('update estimate settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.estimate_settings_updated'),
        ]);
    }

    /**
     * Update the company's payment settings.
     *
     * @param PaymentUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function payment(PaymentUpdate $request)
    {
        Gate::authorize('update payment settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.payment_settings_updated'),
        ]);
    }

    /**
     * Update the company's product settings.
     *
     * @param ProductUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product(ProductUpdate $request)
    {
        Gate::authorize('update product settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.product_settings_updated'),
        ]);
    }

    /**
     * Update the company's email template settings.
     *
     * @param EmailTemplateUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function email_templates(EmailTemplateUpdate $request)
    {
        Gate::authorize('update email templates');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        return $this->sendResponse($request->currentCompany, true, 200, [
            'message' => __('messages.email_templates_updated'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
