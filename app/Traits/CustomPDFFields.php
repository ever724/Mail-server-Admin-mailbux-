<?php

namespace App\Traits;

use App\Models\CompanySetting;

trait CustomPDFFields
{
    /**
     * Get From Field for PDF views.
     *
     * @param string $key
     *
     * @return string
     */
    public function getField($key)
    {
        return $this->replaceFieldTags(CompanySetting::getSetting($key, $this->company->id));
    }

    /**
     * Build the from field.
     *
     * @param mixed $text
     *
     * @return $this
     */
    public function replaceFieldTags($text)
    {
        $tag_list = [];

        if ($this->company) {
            $tag_list['{company.name}'] = $this->company->name;
            $tag_list['{company.vat_number}'] = $this->company->vat_number ?? '';
            $tag_list['{company.billing.address_1}'] = $this->company->billing ? $this->company->billing->address_1 : '';
            $tag_list['{company.billing.address_2}'] = $this->company->billing ? $this->company->billing->address_2 : '';
            $tag_list['{company.billing.city}'] = $this->company->billing ? $this->company->billing->city : '';
            $tag_list['{company.billing.state}'] = $this->company->billing ? $this->company->billing->state : '';
            $tag_list['{company.billing.country}'] = $this->company->billing ? ($this->company->billing->country ? $this->company->billing->country->name : '') : '';
            $tag_list['{company.billing.phone}'] = $this->company->billing ? $this->company->billing->phone : '';
            $tag_list['{company.billing.zip}'] = $this->company->billing ? $this->company->billing->zip : '';
        }

        if ($this->customer) {
            $tag_list['{customer.name}'] = $this->customer->display_name;
            $tag_list['{customer.vat_number}'] = $this->customer->billing ? $this->customer->vat_number : '';
            $tag_list['{customer.billing.address_1}'] = $this->customer->billing ? $this->customer->billing->address_1 : '';
            $tag_list['{customer.billing.address_2}'] = $this->customer->billing ? $this->customer->billing->address_2 : '';
            $tag_list['{customer.billing.city}'] = $this->customer->billing ? $this->customer->billing->city : '';
            $tag_list['{customer.billing.state}'] = $this->customer->billing ? $this->customer->billing->state : '';
            $tag_list['{customer.billing.country}'] = $this->customer->billing ? ($this->customer->billing->country ? $this->customer->billing->country->name : '') : '';
            $tag_list['{customer.billing.phone}'] = $this->customer->billing ? $this->customer->billing->phone : '';
            $tag_list['{customer.billing.zip}'] = $this->customer->billing ? $this->customer->billing->zip : '';
            $tag_list['{customer.shipping.address_1}'] = $this->customer->shipping ? $this->customer->shipping->address_1 : '';
            $tag_list['{customer.shipping.address_2}'] = $this->customer->shipping ? $this->customer->shipping->address_2 : '';
            $tag_list['{customer.shipping.city}'] = $this->customer->shipping ? $this->customer->shipping->city : '';
            $tag_list['{customer.shipping.state}'] = $this->customer->shipping ? $this->customer->shipping->state : '';
            $tag_list['{customer.shipping.country}'] = $this->customer->shipping ? ($this->customer->shipping->country ? $this->customer->shipping->country->name : '') : '';
            $tag_list['{customer.shipping.phone}'] = $this->customer->shipping ? $this->customer->shipping->phone : '';
            $tag_list['{customer.shipping.zip}'] = $this->customer->shipping ? $this->customer->shipping->zip : '';
        }

        if ($this->vendor) {
            $tag_list['{vendor.name}'] = $this->vendor->display_name;
            $tag_list['{vendor.vat_number}'] = $this->vendor->billing ? $this->vendor->vat_number : '';
            $tag_list['{vendor.billing.address_1}'] = $this->vendor->billing ? $this->vendor->billing->address_1 : '';
            $tag_list['{vendor.billing.address_2}'] = $this->vendor->billing ? $this->vendor->billing->address_2 : '';
            $tag_list['{vendor.billing.city}'] = $this->vendor->billing ? $this->vendor->billing->city : '';
            $tag_list['{vendor.billing.state}'] = $this->vendor->billing ? $this->vendor->billing->state : '';
            $tag_list['{vendor.billing.country}'] = $this->vendor->billing ? ($this->vendor->billing->country ? $this->vendor->billing->country->name : '') : '';
            $tag_list['{vendor.billing.phone}'] = $this->vendor->billing ? $this->vendor->billing->phone : '';
            $tag_list['{vendor.billing.zip}'] = $this->vendor->billing ? $this->vendor->billing->zip : '';
            $tag_list['{vendor.shipping.address_1}'] = $this->vendor->shipping ? $this->vendor->shipping->address_1 : '';
            $tag_list['{vendor.shipping.address_2}'] = $this->vendor->shipping ? $this->vendor->shipping->address_2 : '';
            $tag_list['{vendor.shipping.city}'] = $this->vendor->shipping ? $this->vendor->shipping->city : '';
            $tag_list['{vendor.shipping.state}'] = $this->vendor->shipping ? $this->vendor->shipping->state : '';
            $tag_list['{vendor.shipping.country}'] = $this->vendor->shipping ? ($this->vendor->shipping->country ? $this->vendor->shipping->country->name : '') : '';
            $tag_list['{vendor.shipping.phone}'] = $this->vendor->shipping ? $this->vendor->shipping->phone : '';
            $tag_list['{vendor.shipping.zip}'] = $this->vendor->shipping ? $this->vendor->shipping->zip : '';
        }

        foreach ($tag_list as $tag => $value) {
            $text = str_replace($tag, $value, $text);
        }

        return $text;
    }
}
