<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Expense\Store;
use App\Http\Requests\Application\Expense\Update;
use App\Interfaces\ExpenseInterface;
use App\Interfaces\VendorInterface;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    // Repository
    private $repository;

    /**
     * Controller constructor.
     *
     * @param ExpenseInterface $repository
     */
    public function __construct(ExpenseInterface $repository, VendorInterface $vendor_repository)
    {
        $this->repository = $repository;
        $this->vendor_repository = $vendor_repository;
    }

    /**
     * Display Expenses Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view expenses');

        return view('application.expenses.index', [
            'expenses' => $this->repository->getPaginatedFilteredExpenses($request),
        ]);
    }

    /**
     * Display the Form for Creating New Expense.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create expense');

        return view('application.expenses.create', [
            'expense' => $this->repository->newExpense($request),
            'vendors' => $this->vendor_repository->getAllVendorsByCompany($request),
        ]);
    }

    /**
     * Store the Expense in Database.
     *
     * @param \App\Http\Requests\Application\Expense\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create expense');

        // Store expense
        $this->repository->createExpense($request);

        session()->flash('alert-success', __('messages.expense_added'));

        return redirect()->route('expenses', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Expense.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update expense');

        return view('application.expenses.edit', [
            'expense' => $this->repository->getExpenseById($request, $request->expense),
            'vendors' => $this->vendor_repository->getAllVendorsByCompany($request),
        ]);
    }

    /**
     * Update the Expense in Database.
     *
     * @param \App\Http\Requests\Application\Expense\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update expense');

        // Update the expense
        $this->repository->updateExpense($request, $request->expense);

        session()->flash('alert-success', __('messages.expense_updated'));

        return redirect()->route('expenses', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Expense.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete expense');

        // Delete Expense
        if ($this->repository->deleteExpense($request, $request->expense)) {
            session()->flash('alert-success', __('messages.expense_deleted'));

            return redirect()->route('expenses', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
