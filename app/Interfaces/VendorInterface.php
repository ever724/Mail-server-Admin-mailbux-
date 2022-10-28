<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface VendorInterface
{
    public function getAllVendorsByCompany(Request $request);

    public function getPaginatedFilteredVendors(Request $request);

    public function newVendor(Request $request);

    public function createVendor(Request $request);

    public function getVendorById(Request $request, $vendor_id);

    public function updateVendor(Request $request, $vendor_id);

    public function deleteVendor(Request $request, $vendor_id);
}
