<?php

namespace App\Repositories;

use App\Interfaces\ProductUnitInterface;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductUnitRepository implements ProductUnitInterface
{
    /**
     * Return paginated and filtered results of product units by company.
     *
     * @param int $company_id
     *
     * @return \App\Models\ProductUnit
     */
    public function getPaginatedFilteredProductUnits(Request $request)
    {
        return ProductUnit::findByCompany($request->currentCompany->id)->latest()->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param int $product_unit_id
     *
     * @return \App\Models\ProductUnit
     */
    public function getProductUnitById(Request $request, $product_unit_id)
    {
        return ProductUnit::findByCompany($request->currentCompany->id)->findOrFail($product_unit_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\ProductUnit
     */
    public function newProductUnit(Request $request)
    {
        $product_unit = new ProductUnit();

        // Fill model with old input
        if (!empty($request->old())) {
            $product_unit->fill($request->old());
        }

        return $product_unit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\ProductUnit
     */
    public function createProductUnit(Request $request)
    {
        // Create Product Unit and Store in Database
        return ProductUnit::create([
            'name' => $request->name,
            'company_id' => $request->currentCompany->id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $product_unit_id
     *
     * @return \App\Models\ProductUnit
     */
    public function updateProductUnit(Request $request, $product_unit_id)
    {
        $product_unit = $this->getProductUnitById($request, $product_unit_id);

        // Update Product Unit in Database
        $product_unit->update([
            'name' => $request->name,
        ]);

        return $product_unit;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $product_unit_id
     *
     * @return bool
     */
    public function deleteProductUnit(Request $request, $product_unit_id)
    {
        $product_unit = $this->getProductUnitById($request, $product_unit_id);

        // Delete Product Unit from Database
        return $product_unit->delete();
    }
}
