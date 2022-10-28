<?php

namespace App\Repositories;

use App\Interfaces\ExpenseCategoryInterface;
use App\Models\ExpenseCategory;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class ExpenseCategoryRepository implements ExpenseCategoryInterface
{
    /**
     * Return paginated and filtered results of expense categories by company.
     *
     * @return \App\Models\ExpenseCategory
     */
    public function getPaginatedFilteredExpenseCategories(Request $request)
    {
        // Apply Filters and Paginate
        return QueryBuilder::for(ExpenseCategory::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('description'),
            ])
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param mixed $expense_category_id
     *
     * @return \App\Models\ExpenseCategory
     */
    public function getExpenseCategoryById(Request $request, $expense_category_id)
    {
        return ExpenseCategory::findByCompany($request->currentCompany->id)->findOrFail($expense_category_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\ExpenseCategory
     */
    public function newExpenseCategory(Request $request)
    {
        $expense_category = new ExpenseCategory();

        // Fill model with old input
        if (!empty($request->old())) {
            $expense_category->fill($request->old());
        }

        return $expense_category;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\ExpenseCategory
     */
    public function createExpenseCategory(Request $request)
    {
        // Create Expense Category and Store in Database
        return ExpenseCategory::create([
            'name' => $request->name,
            'company_id' => $request->currentCompany->id,
            'description' => $request->description,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $expense_category_id
     *
     * @return \App\Models\ExpenseCategory
     */
    public function updateExpenseCategory(Request $request, $expense_category_id)
    {
        $expense_category = $this->getExpenseCategoryById($request, $expense_category_id);

        // Update Expense Category in Database
        $expense_category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $expense_category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $expense_category_id
     *
     * @return bool
     */
    public function deleteExpenseCategory(Request $request, $expense_category_id)
    {
        $expense_category = $this->getExpenseCategoryById($request, $expense_category_id);

        // Delete Expense Category from Database
        return $expense_category->delete();
    }
}
