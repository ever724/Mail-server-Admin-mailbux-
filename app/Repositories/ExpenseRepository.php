<?php

namespace App\Repositories;

use App\Interfaces\ExpenseInterface;
use App\Models\Expense;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseRepository implements ExpenseInterface
{
    /**
     * Return paginated and filtered results of expenses by company.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Expense
     */
    public function getPaginatedFilteredExpenses(Request $request)
    {
        return QueryBuilder::for(Expense::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::exact('expense_category_id'),
                AllowedFilter::exact('vendor_id'),
                AllowedFilter::exact('is_recurring'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
            ])
            ->allowedIncludes([
                'company',
                'vendor',
                'category',
            ])
            ->oldest()
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single expense by id.
     *
     * @param int $expense_id
     *
     * @return \App\Models\Expense
     */
    public function getExpenseById(Request $request, $expense_id)
    {
        return Expense::with(['vendor', 'company', 'category'])->findByCompany($request->currentCompany->id)->findOrFail($expense_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\Expense
     */
    public function newExpense(Request $request)
    {
        $expense = new Expense();

        // Fill model with old input
        if (!empty($request->old())) {
            $expense->fill($request->old());
        }

        return $expense;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Expense
     */
    public function createExpense(Request $request)
    {
        $company = $request->currentCompany;

        // Create Expense and Store in Database
        $expense = Expense::create([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'company_id' => $company->id,
            'vendor_id' => $request->vendor_id,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
            'is_recurring' => $request->is_recurring,
            'cycle' => $request->cycle,
        ]);

        // Set next recurring date
        if ($expense->is_recurring) {
            $expense->next_recurring_at = Carbon::parse($expense->expense_date)->addMonths($expense->is_recurring)->format('Y-m-d');
            $expense->save();
        }

        // Add custom field values
        $expense->addCustomFields($request->custom_fields);

        // Upload Receipt File
        if ($request->receipt) {
            $request->validate(['receipt' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->receipt->storeAs('receipts', 'receipt-' . $expense->id . '.' . $request->receipt->getClientOriginalExtension(), 'public_dir');
            $expense->receipt = asset('/uploads/' . $path);
            $expense->save();
        }

        return $expense;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $expense_id
     *
     * @return \App\Models\Expense
     */
    public function updateExpense(Request $request, $expense_id)
    {
        $expense = $this->getExpenseById($request, $expense_id);

        // Update the Expense
        $expense->update([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'vendor_id' => $request->vendor_id,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
            'is_recurring' => $request->is_recurring,
            'cycle' => $request->cycle,
        ]);

        // Set next recurring date
        if ($expense->is_recurring) {
            $expense->next_recurring_at = Carbon::parse($expense->expense_date)->addMonths($expense->is_recurring)->format('Y-m-d');
            $expense->save();
        }

        // Update custom field values
        $expense->updateCustomFields($request->custom_fields);

        // Upload Receipt File
        if ($request->receipt) {
            $request->validate(['receipt' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->receipt->storeAs('receipts', 'receipt-' . $expense->id . '.' . $request->receipt->getClientOriginalExtension(), 'public_dir');
            $expense->receipt = asset('/uploads/' . $path);
            $expense->save();
        }

        return $expense;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $expense_id
     *
     * @return bool
     */
    public function deleteExpense(Request $request, $expense_id)
    {
        return Expense::destroy($expense_id);
    }
}
