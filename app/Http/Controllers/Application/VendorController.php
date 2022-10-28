<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Vendor\Store;
use App\Http\Requests\Application\Vendor\Update;
use App\Interfaces\VendorInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param VendorInterface $repository
     */
    public function __construct(VendorInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display Vendors Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view vendors');

        return view('application.vendors.index', [
            'vendors' => $this->repository->getPaginatedFilteredVendors($request),
        ]);
    }

    /**
     * Display the Form for Creating New Vendor.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create vendor');

        return view('application.vendors.create', [
            'vendor' => $this->repository->newVendor($request),
        ]);
    }

    /**
     * Store the Vendor in Database.
     *
     * @param \App\Http\Requests\Application\Vendor\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create vendor');

        // Store Vendor
        $vendor = $this->repository->createVendor($request);

        session()->flash('alert-success', __('messages.vendor_added'));

        return redirect()->route('vendors.details', [
            'vendor' => $vendor->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Vendor Details Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        Gate::authorize('view vendors');

        $vendor = $this->repository->getVendorById($request, $request->vendor);

        return view('application.vendors.details', [
            'vendor' => $vendor,
            'expenses' => $vendor->expenses()->orderBy('created_at')->paginate(50),
        ]);
    }

    /**
     * Display the Form for Editing Vendor.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update vendor');

        return view('application.vendors.edit', [
            'vendor' => $this->repository->getVendorById($request, $request->vendor),
        ]);
    }

    /**
     * Update the Vendor in Database.
     *
     * @param \App\Http\Requests\Application\Vendor\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update vendor');

        // Update Vendor
        $vendor = $this->repository->updateVendor($request, $request->vendor);

        session()->flash('alert-success', __('messages.vendor_updated'));

        return redirect()->route('vendors.details', [
            'vendor' => $vendor->id,
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Vendor.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete vendor');

        // Delete Vendor
        if ($this->repository->deleteVendor($request, $request->vendor)) {
            session()->flash('alert-success', __('messages.vendor_deleted'));

            return redirect()->route('vendors', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
