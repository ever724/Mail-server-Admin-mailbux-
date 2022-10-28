<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Expense\Store;
use App\Http\Requests\Application\Expense\Update;
use App\Http\Resources\ExpenseResource;
use App\Interfaces\ExpenseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends BaseController
{
    // Resource
    public $resource = ExpenseResource::class;

    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param ExpenseInterface $repository
     */
    public function __construct(ExpenseInterface $repository)
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
        Gate::authorize('view expenses');

        $expenses = $this->repository->getPaginatedFilteredExpenses($request);

        return $this->sendCollectionResponse($expenses, true, 200);
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
        Gate::authorize('create expense');

        // Store expense
        $expense = $this->repository->createExpense($request);

        return $this->sendResponse($expense, true, 201, [
            'message' => __('messages.expense_added'),
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
        Gate::authorize('view expenses');

        $expense = $this->repository->getExpenseById($request, $request->expense);

        return $this->sendResponse($expense, true, 200);
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
        Gate::authorize('update expense');

        // Update the expense
        $expense = $this->repository->updateExpense($request, $request->expense);

        return $this->sendResponse($expense, true, 200, [
            'message' => __('messages.expense_updated'),
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
        Gate::authorize('delete expense');

        // Delete Expense
        if ($this->repository->deleteExpense($request, $request->expense)) {
            return $this->sendResponse([], true, 200, [
                'message' => __('messages.expense_deleted'),
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
