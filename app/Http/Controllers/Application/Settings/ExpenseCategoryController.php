<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\Settings\ExpenseCategory\Store;
use App\Http\Requests\Application\Settings\ExpenseCategory\Update;
use App\Interfaces\ExpenseCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoryController extends Controller
{
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
     * Display Expense Category Settings Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view expense categories');

        return view('application.settings.expense_category.index', [
            'expense_categories' => $this->repository->getPaginatedFilteredExpenseCategories($request),
        ]);
    }

    /**
     * Display the Form for Creating New Expense Category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create expense category');

        return view('application.settings.expense_category.create', [
            'expense_category' => $this->repository->newExpenseCategory($request),
        ]);
    }

    /**
     * Store the Expense Category in Database.
     *
     * @param \App\Http\Requests\Application\Settings\ExpenseCategory\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        Gate::authorize('create expense category');

        // Store Custom Field
        $this->repository->createExpenseCategory($request);

        session()->flash('alert-success', __('messages.expense_category_added'));

        return redirect()->route('settings.expense_categories', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Display the Form for Editing Expense Category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        Gate::authorize('update expense category');

        return view('application.settings.expense_category.edit', [
            'expense_category' => $this->repository->getExpenseCategoryById($request, $request->expense_category),
        ]);
    }

    /**
     * Update the Expense Category.
     *
     * @param \App\Http\Requests\Application\Settings\ExpenseCategory\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request)
    {
        Gate::authorize('update expense category');

        // Update Expense Category
        $this->repository->updateExpenseCategory($request, $request->expense_category);

        session()->flash('alert-success', __('messages.expense_category_updated'));

        return redirect()->route('settings.expense_categories', [
            'company_uid' => $request->currentCompany->uid,
        ]);
    }

    /**
     * Delete the Expense Category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        Gate::authorize('delete expense category');

        // Delete Expense Category
        if ($this->repository->deleteExpenseCategory($request, $request->expense_category)) {
            session()->flash('alert-success', __('messages.expense_category_deleted'));

            return redirect()->route('settings.expense_categories', [
                'company_uid' => $request->currentCompany->uid,
            ]);
        }

        return redirect()->back();
    }
}
