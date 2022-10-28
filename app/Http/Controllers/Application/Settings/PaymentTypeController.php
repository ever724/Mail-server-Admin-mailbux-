<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\PaymentType\Store;
use App\Http\Requests\Application\Settings\PaymentType\Update;
use App\Interfaces\PaymentTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentTypeController extends Controller
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
     * Display the Form for Creating New Payment Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create payment type');

        return view('application.settings.payment.types.create', [
            'payment_type' => $this->repository->newPaymentType($request),
        ]);
    }

    /**
     * Store the Payment Method in Database.
     *
     * @param \App\Http\Requests\Application\Settings\PaymentType\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create payment type');

        // Store Payment Type
        $this->repository->createPaymentType($request);

        session()->flash('alert-success', __('messages.payment_type_category_added'));

        return redirect()->route('settings.payment', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Payment Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update payment type');

        return view('application.settings.payment.types.edit', [
            'payment_type' => $this->repository->getPaymentTypeById($request, $request->type),
        ]);
    }

    /**
     * Update the Payment Type.
     *
     * @param \App\Http\Requests\Application\Settings\PaymentType\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update payment type');

        // Update Payment Type
        $this->repository->updatePaymentType($request, $request->type);

        session()->flash('alert-success', __('messages.payment_type_category_updated'));

        return redirect()->route('settings.payment', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Payment Type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete payment type');

        // Delete Payment Type
        if ($this->repository->deletePaymentType($request, $request->type)) {
            session()->flash('alert-success', __('messages.payment_type_category_deleted'));

            return redirect()->route('settings.payment', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
