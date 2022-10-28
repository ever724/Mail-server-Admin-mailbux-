<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Customer\Store;
use App\Http\Requests\Application\Customer\Update;
use App\Interfaces\CustomerInterface;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class CustomerController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param CustomerInterface $repository
     */
    public function __construct(CustomerInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Customers Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view customers');

        return view('application.customers.index', [
            'customers' => $this->repository->getPaginatedFilteredCustomers($request),
        ]);
    }

    /**
     * Display the Form for Creating New Customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('create customer');

        return view('application.customers.create', [
            'customer' => new Customer(),
        ]);
    }

    /**
     * Store the Customer in Database.
     *
     * @param \App\Http\Requests\Application\Customer\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create customer');

        // Check if the subscription limit is reached
        /*  if (!$request->currentCompany->subscription('main')->canUseFeature('customers')) {
            session()->flash('alert-danger', __('messages.you_have_reached_the_limit'));
            return redirect()->back();
        } */

        // Store Customer
        $customer = $this->repository->createCustomer($request);

        session()->flash('alert-success', __('messages.customer_added'));

        return redirect()->route('customers.details', [
            'customer' => $customer->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Customer Details Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        Gate::authorize('view customers');

        $customer = $this->repository->getCustomerById($request, $request->customer);

        return view('application.customers.details', [
            'customer' => $customer,
            'invoices' => $customer->invoices()->orderBy('invoice_number')->paginate(50),
            'estimates' => $customer->estimates()->orderBy('estimate_number')->paginate(50),
            'payments' => $customer->payments()->orderBy('payment_number')->paginate(50),
            'activities' => Activity::where('subject_id', $customer->id)->get(),
        ]);
    }

    /**
     * Display the Form for Editing Customer.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update customer');

        return view('application.customers.edit', [
            'customer' => $this->repository->getCustomerById($request, $request->customer),
        ]);
    }

    /**
     * Update the Customer in Database.
     *
     * @param \App\Http\Requests\Application\Customer\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update customer');

        // Update Customer
        $customer = $this->repository->updateCustomer($request, $request->customer);

        session()->flash('alert-success', __('messages.customer_updated'));

        return redirect()->route('customers.details', [
            'customer' => $customer->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Customer.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete customer');

        // Delete Customer
        if ($this->repository->deleteCustomer($request, $request->customer)) {
            session()->flash('alert-success', __('messages.customer_deleted'));

            return redirect()->route('customers', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
