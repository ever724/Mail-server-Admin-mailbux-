<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\CurrencyResource;
use App\Models\Currency;

class CurrencyController extends BaseController
{
    // Resource
    public $resource = CurrencyResource::class;

    /**
     * Get currencies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $currencies = Currency::all();

        return $this->sendCollectionResponse($currencies, true, 200);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
