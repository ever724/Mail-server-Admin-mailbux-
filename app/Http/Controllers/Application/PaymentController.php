<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Payment\Store;
use App\Http\Requests\Application\Payment\Update;
use App\Interfaces\PaymentInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param PaymentInterface $repository
     */
    public function __construct(PaymentInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Payments Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view payments');

        return view('application.payments.index', [
            'payments' => $this->repository->getPaginatedFilteredPayments($request),
        ]);
    }

    /**
     * Display the Form for Creating New Payment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create payment');

        return view('application.payments.create', [
            'payment' => $this->repository->newPayment($request),
        ]);
    }

    /**
     * Store the Payments in Database.
     *
     * @param \App\Http\Requests\Application\Payment\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create payment');

        $this->repository->createPayment($request);

        session()->flash('alert-success', __('messages.payment_added'));

        return redirect()->route('payments', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Payment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update payment');

        return view('application.payments.edit', [
            'payment' => $this->repository->getPaymentById($request, $request->payment),
        ]);
    }

    /**
     * Update the Payment in Database.
     *
     * @param \App\Http\Requests\Application\Payment\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update payment');

        // Update payment
        $this->repository->updatePayment($request, $request->payment);

        session()->flash('alert-success', __('messages.payment_updated'));

        return redirect()->route('payments', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Payment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete payment');

        // Delete payment
        if ($this->repository->deletePayment($request, $request->payment)) {
            session()->flash('alert-success', __('messages.payment_deleted'));

            return redirect()->route('payments', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
