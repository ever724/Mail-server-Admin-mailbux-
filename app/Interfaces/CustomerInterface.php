<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface CustomerInterface
{
    public function getPaginatedFilteredCustomers(Request $request);

    public function getCustomerById(Request $request, $customer_id);

    public function createCustomer(Request $request);

    public function updateCustomer(Request $request, $customer_id);

    public function deleteCustomer(Request $request, $customer_id);
}
