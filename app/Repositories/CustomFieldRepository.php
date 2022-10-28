<?php

namespace App\Repositories;

use App\Interfaces\CustomFieldInterface;
use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldRepository implements CustomFieldInterface
{
    /**
     * Return a listing of the resource.
     *
     * @return \App\Models\CustomField
     */
    public function getAllCustomFields(Request $request)
    {
        return CustomField::findByCompany($request->currentCompany->id)->get();
    }

    /**
     * Return paginated and filtered results of custom fields by company.
     *
     * @return \App\Models\CustomField
     */
    public function getPaginatedFilteredCustomFields(Request $request)
    {
        return CustomField::findByCompany($request->currentCompany->id)->latest()->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param mixed $custom_field_id
     *
     * @return \App\Models\CustomField
     */
    public function getCustomFieldById(Request $request, $custom_field_id)
    {
        return CustomField::findByCompany($request->currentCompany->id)->findOrFail($custom_field_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\CustomField
     */
    public function newCustomField(Request $request)
    {
        $custom_field = new CustomField();

        // Fill model with old input
        if (!empty($request->old())) {
            $custom_field->fill($request->old());
        }

        return $custom_field;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\CustomField
     */
    public function createCustomField(Request $request)
    {
        $company = $request->currentCompany;
        // Create Custom Field and Store in Database
        $data = $request->validated();
        $data['model_type'] = "App\\Models\\{$request->model_type}";
        $data[get_custom_field_value_key($request->type)] = $request->default_value;
        $data['company_id'] = $company->id;

        return CustomField::create($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $custom_field_id
     *
     * @return \App\Models\CustomField
     */
    public function updateCustomField(Request $request, $custom_field_id)
    {
        $custom_field = $this->getCustomFieldById($request, $custom_field_id);

        // Update Custom Field in Database
        $data = $request->validated();
        $data['model_type'] = "App\\Models\\{$request->model_type}";
        $data[get_custom_field_value_key($request->type)] = $request->default_value ?? null;
        $custom_field->update($data);

        return $custom_field;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $custom_field_id
     *
     * @return bool
     */
    public function deleteCustomField(Request $request, $custom_field_id)
    {
        $custom_field = $this->getCustomFieldById($request, $custom_field_id);

        // Check if the CustomField is already in use
        if ($custom_field->custom_field_values()->exists()) {
            session()->flash('alert-error', __('messages.custom_field_is_in_use'));

            return false;
        }

        // Delete Custom Field from Database
        return $custom_field->delete();
    }
}
