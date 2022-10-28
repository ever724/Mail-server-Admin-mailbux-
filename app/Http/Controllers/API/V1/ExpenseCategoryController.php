<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\ExpenseCategory\Store;
use App\Http\Requests\Application\Settings\ExpenseCategory\Update;
use App\Http\Resources\ExpenseCategoryResource;
use App\Interfaces\ExpenseCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoryController extends BaseController
{
    // Resource
    public $resource = ExpenseCategoryResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param ExpenseCategoryInterface $repository
     */
    public function __construct(ExpenseCategoryInterface $repository)
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
        Gate::authorize('view expense categories');

        $expense_categories = $this->repository->getPaginatedFilteredExpenseCategories($request);

        return $this->sendCollectionResponse($expense_categories, true, 200);
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
        Gate::authorize('create expense category');

        // Store Custom Field
        $expense_category = $this->repository->createExpenseCategory($request);

        return $this->sendResponse($expense_category, true, 201, [
            'message' => __('messages.expense_category_added'),
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
        Gate::authorize('view expense categories');

        $expense_category = $this->repository->getExpenseCategoryById($request, $request->expense_category);

        return $this->sendResponse($expense_category, true, 200);
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
        Gate::authorize('update expense category');

        // Update Expense Category
        $expense_category = $this->repository->updateExpenseCategory($request, $request->expense_category);

        return $this->sendResponse($expense_category, true, 200, [
            'message' => __('messages.expense_category_updated'),
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
        Gate::authorize('delete expense category');

        // Delete Expense Category
        if ($this->repository->deleteExpenseCategory($request, $request->expense_category)) {
            return $this->sendResponse(null, true, 200, [
                'message' => __('messages.expense_category_deleted'),
            ]);
        }

        return $this->sendResponse(null, false, 500, [
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
