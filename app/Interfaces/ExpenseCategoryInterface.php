<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ExpenseCategoryInterface
{
    public function getPaginatedFilteredExpenseCategories(Request $request);

    public function newExpenseCategory(Request $request);

    public function createExpenseCategory(Request $request);

    public function getExpenseCategoryById(Request $request, $expense_category_id);

    public function updateExpenseCategory(Request $request, $expense_category_id);

    public function deleteExpenseCategory(Request $request, $expense_category_id);
}
