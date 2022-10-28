<?php

namespace App\Repositories;

use App\Interfaces\TaxTypeInterface;
use App\Models\TaxType;
use Illuminate\Http\Request;

class TaxTypeRepository implements TaxTypeInterface
{
    /**
     * Return paginated and filtered results of tax types by company.
     *
     * @param int $company_id
     *
     * @return \App\Models\TaxType
     */
    public function getPaginatedFilteredTaxTypes(Request $request)
    {
        return TaxType::findByCompany($request->currentCompany->id)->latest()->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param int $tax_type_id
     *
     * @return \App\Models\TaxType
     */
    public function getTaxTypeById(Request $request, $tax_type_id)
    {
        return TaxType::findByCompany($request->currentCompany->id)->findOrFail($tax_type_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\TaxType
     */
    public function newTaxType(Request $request)
    {
        $tax_type = new TaxType();

        // Fill model with old input
        if (!empty($request->old())) {
            $tax_type->fill($request->old());
        }

        return $tax_type;
    }

    /**
     * Store Tax Type on database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\TaxType
     */
    public function createTaxType(Request $request)
    {
        // Create Tax Type and Store in Database
        return TaxType::create([
            'name' => $request->name,
            'company_id' => $request->currentCompany->id,
            'percent' => $request->percent,
            'description' => $request->description,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $tax_type_id
     *
     * @return \App\Models\TaxType
     */
    public function updateTaxType(Request $request, $tax_type_id)
    {
        $tax_type = $this->getTaxTypeById($request, $tax_type_id);

        // Update Tax Type in Database
        $tax_type->update([
            'name' => $request->name,
            'percent' => $request->percent,
            'description' => $request->description,
        ]);

        return $tax_type;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $tax_type_id
     *
     * @return bool
     */
    public function deleteTaxType(Request $request, $tax_type_id)
    {
        $tax_type = $this->getTaxTypeById($request, $tax_type_id);

        // Check if the Tax is already in use
        if ($tax_type->taxes() && $tax_type->taxes()->count() > 0) {
            session()->flash('alert-error', __('messages.tax_type_is_in_use'));

            return false;
        }

        // Delete Tax Type from Database
        return $tax_type->delete();
    }
}
