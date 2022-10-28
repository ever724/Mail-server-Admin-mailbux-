<?php

namespace App\Repositories;

use App\Interfaces\CustomerInterface;
use App\Models\Customer;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerInterface
{
    /**
     * Return paginated and filtered results of customers by company.
     *
     * @return \App\Models\Customer
     */
    public function getPaginatedFilteredCustomers(Request $request)
    {
        return QueryBuilder::for(Customer::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::partial('display_name'),
                AllowedFilter::partial('contact_name'),
                AllowedFilter::partial('email'),
                AllowedFilter::scope('has_unpaid'),
            ])
            ->allowedIncludes([
                'company',
            ])
            ->oldest()
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single customer by id.
     *
     * @param mixed $customer_id
     *
     * @return \App\Models\Customer
     */
    public function getCustomerById(Request $request, $customer_id)
    {
        return Customer::with(['company'])->findByCompany($request->currentCompany->id)->findOrFail($customer_id);
    }

    /**
     * Create a Customer.
     *
     * @return \App\Models\Customer
     */
    public function createCustomer(Request $request)
    {
        $company = $request->currentCompany;

        // Create Customer and Store in Database
        $customer = Customer::create([
            'company_id' => $company->id,
            'display_name' => $request->display_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'website' => $request->website,
            'currency_id' => $request->currency_id,
            'vat_number' => $request->vat_number,
            'password' => $request->password ?? Hash::make(Str::random(8)),
        ]);

        // Set Customer's billing and shipping addresses
        $customer->address('billing', $request->input('billing'));
        $customer->address('shipping', $request->input('shipping'));

        // Add custom field values
        $customer->addCustomFields($request->custom_fields);

        // Record product
        //$company->subscription('main')->recordFeatureUsage('customers');

        return $customer;
    }

    /**
     * Update a Customer.
     *
     * @param mixed $customer_id
     *
     * @return \App\Models\Customer
     */
    public function updateCustomer(Request $request, $customer_id)
    {
        $customer = $this->getCustomerById($request, $customer_id);

        // Update Customer in Database
        $customer->update([
            'display_name' => $request->display_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'website' => $request->website,
            'currency_id' => $request->currency_id,
            'vat_number' => $request->vat_number,
        ]);

        if ($request->password && $request->password != '') {
            $customer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update Customer's billing and shipping addresses
        $customer->updateAddress('billing', $request->input('billing'));
        $customer->updateAddress('shipping', $request->input('shipping'));

        // Update custom field values
        $customer->updateCustomFields($request->custom_fields);

        return $customer;
    }

    /**
     * Delete a Customer.
     *
     * @param int $customer_id
     *
     * @return bool
     */
    public function deleteCustomer(Request $request, $customer_id)
    {
        $customer = $this->getCustomerById($request, $customer_id);

        // Reduce feature
        $customer->company->subscription('main')->reduceFeatureUsage('customers');

        // Delete Customer's Estimates from Database
        if ($customer->estimates()->exists()) {
            $customer->estimates()->delete();
        }

        // Delete Customer's Invoices from Database
        if ($customer->invoices()->exists()) {
            $customer->invoices()->delete();
        }

        // Delete Customer's Payments from Database
        if ($customer->payments()->exists()) {
            $customer->payments()->delete();
        }

        // Delete Customer's Addresses from Database
        if ($customer->addresses()->exists()) {
            $customer->addresses()->delete();
        }

        // Delete Customer from Database
        return $customer->delete();
    }
}
