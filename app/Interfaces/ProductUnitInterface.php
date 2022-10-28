<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ProductUnitInterface
{
    public function getPaginatedFilteredProductUnits(Request $request);

    public function newProductUnit(Request $request);

    public function createProductUnit(Request $request);

    public function getProductUnitById(Request $request, $product_unit_id);

    public function updateProductUnit(Request $request, $product_unit_id);

    public function deleteProductUnit(Request $request, $product_unit_id);
}
