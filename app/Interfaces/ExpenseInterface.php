<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ExpenseInterface
{
    public function getPaginatedFilteredExpenses(Request $request);

    public function getExpenseById(Request $request, $expense_id);

    public function newExpense(Request $request);

    public function createExpense(Request $request);

    public function updateExpense(Request $request, $expense_id);

    public function deleteExpense(Request $request, $expense_id);
}
