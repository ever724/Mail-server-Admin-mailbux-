<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Interfaces\PlanInterface;
use Illuminate\Http\Request;

class PlanController extends BaseController
{
    // Resource
    public $resource = PlanResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param PlanInterface $repository
     */
    public function __construct(PlanInterface $repository)
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
        $plans = $this->repository->getPlans($request);

        return $this->sendCollectionResponse($plans);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
