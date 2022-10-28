<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\CustomField\Store;
use App\Http\Requests\Application\Settings\CustomField\Update;
use App\Http\Resources\CustomFieldResource;
use App\Interfaces\CustomFieldInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomFieldController extends BaseController
{
    // Resource
    public $resource = CustomFieldResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param CustomFieldInterface $repository
     */
    public function __construct(CustomFieldInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Gate::authorize('view custom fields');

        $custom_fields = $this->repository->getPaginatedFilteredCustomFields($request);

        return $this->sendCollectionResponse($custom_fields, true, 200);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Store $request)
    {
        Gate::authorize('create custom field');

        // Store Custom Field
        $custom_field = $this->repository->createCustomField($request, $request->currentCompany);

        return $this->sendResponse($custom_field, true, 201, [
            'message' => __('messages.custom_field_created'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        Gate::authorize('view custom fields');

        $custom_field = $this->repository->getCustomFieldById($request, $request->custom_field);

        return $this->sendResponse($custom_field, true, 200);
    }

    /**
     * Update the specified resource in database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request)
    {
        Gate::authorize('update custom field');

        // Update Custom Field
        $custom_field = $this->repository->updateCustomField($request, $request->custom_field);

        return $this->sendResponse($custom_field, true, 200, [
            'message' => __('messages.custom_field_updated'),
        ]);
    }

    /**
     * Delete the specified resource from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        Gate::authorize('delete custom field');

        // Delete Custom Field
        if ($this->repository->deleteCustomField($request, $request->custom_field)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.custom_field_deleted'),
            ]);
        }

        return $this->sendResponse([], false, 500, [
            'message' => session()->get('alert-danger'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
