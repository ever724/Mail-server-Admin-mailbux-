<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface CustomFieldInterface
{
    public function getAllCustomFields(Request $request);

    public function getPaginatedFilteredCustomFields(Request $request);

    public function getCustomFieldById(Request $request, $custom_field_id);

    public function newCustomField(Request $request);

    public function createCustomField(Request $request);

    public function updateCustomField(Request $request, $custom_field_id);

    public function deleteCustomField(Request $request, $custom_field_id);
}
