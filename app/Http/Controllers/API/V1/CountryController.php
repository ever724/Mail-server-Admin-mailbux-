<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\CountryResource;
use App\Models\Country;

class CountryController extends BaseController
{
    // Resource
    public $resource = CountryResource::class;

    /**
     * Get countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $countries = Country::all();

        return $this->sendCollectionResponse($countries, true, 200);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
