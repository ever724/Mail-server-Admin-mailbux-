<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\Payment\Update;
use App\Interfaces\PaymentTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param PaymentTypeInterface $repository
     */
    public function __construct(PaymentTypeInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Payment Settings Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('application.settings.payment.index', [
            'payment_types' => $this->repository->getPaginatedFilteredPaymentTypes($request),
        ]);
    }

    /**
     * Update the Payment Settings.
     *
     * @param \App\Http\Requests\Application\Settings\Payment\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update payment settings');

        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('messages.payment_settings_updated'));

        return redirect()->route('settings.payment', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }
}
