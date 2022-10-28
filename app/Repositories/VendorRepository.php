<?php

namespace App\Repositories;

use App\Interfaces\VendorInterface;
use App\Models\Vendor;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class VendorRepository implements VendorInterface
{
    public function getAllVendorsByCompany(Request $request)
    {
        return Vendor::findByCompany($request->currentCompany->id)->get();
    }

    /**
     * Return paginated and filtered results of vendors by company.
     *
     * @param int $company_id
     *
     * @return \App\Models\Vendor
     */
    public function getPaginatedFilteredVendors(Request $request)
    {
        return QueryBuilder::for(Vendor::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::partial('display_name'),
                AllowedFilter::partial('contact_name'),
                AllowedFilter::partial('email'),
            ])
            ->allowedIncludes([
                'company',
            ])
            ->oldest()
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single vendor by id.
     *
     * @param int $vendor_id
     *
     * @return \App\Models\Vendor
     */
    public function getVendorById(Request $request, $vendor_id)
    {
        return Vendor::with(['company'])->findByCompany($request->currentCompany->id)->findOrFail($vendor_id);
    }

    /**
     * Create an instance of Vendor.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\Vendor
     */
    public function newVendor(Request $request)
    {
        $vendor = new Vendor();

        // Fill model with old input
        if (!empty($request->old())) {
            $vendor->fill($request->old());
        }

        return $vendor;
    }

    /**
     * Store an instance of Vendor.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Vendor
     */
    public function createVendor(Request $request)
    {
        // Create Vendor and Store in Database
        $vendor = Vendor::create([
            'company_id' => $request->currentCompany->id,
            'display_name' => $request->display_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'website' => $request->website,
        ]);

        // Add custom field values
        $vendor->addCustomFields($request->custom_fields);

        // Set Vendor's Billing Address
        $vendor->address('billing', $request->input('billing'));

        return $vendor;
    }

    /**
     * Update an instance of Vendor.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $vendor_id
     *
     * @return \App\Models\Vendor
     */
    public function updateVendor(Request $request, $vendor_id)
    {
        $vendor = $this->getVendorById($request, $vendor_id);

        // Update Vendor in Database
        $vendor->update([
            'display_name' => $request->display_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'website' => $request->website,
        ]);

        // Update custom field values
        $vendor->updateCustomFields($request->custom_fields);

        // Update Vendor's billing address
        $vendor->updateAddress('billing', $request->input('billing'));

        return $vendor;
    }

    /**
     * Delete an instance of Vendor.
     *
     * @param int $vendor_id
     *
     * @return bool
     */
    public function deleteVendor(Request $request, $vendor_id)
    {
        $vendor = $this->getVendorById($request, $vendor_id);

        // Delete Vendor's Expenses from Database
        if ($vendor->expenses()->exists()) {
            $vendor->expenses()->delete();
        }

        // Delete Vendor's Addresses from Database
        if ($vendor->addresses()->exists()) {
            $vendor->addresses()->delete();
        }

        // Delete Vendor from Database
        return $vendor->delete();
    }
}
