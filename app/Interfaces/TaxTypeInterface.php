<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TaxTypeInterface
{
    public function getPaginatedFilteredTaxTypes(Request $request);

    public function newTaxType(Request $request);

    public function createTaxType(Request $request);

    public function getTaxTypeById(Request $request, $custom_field_id);

    public function updateTaxType(Request $request, $custom_field_id);

    public function deleteTaxType(Request $request, $custom_field_id);
}
