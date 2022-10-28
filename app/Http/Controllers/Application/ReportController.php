<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Get Customer Sales Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_sales()
    {
        Gate::authorize('view customer sales report');

        return view('application.reports.customer_sales');
    }

    /**
     * Get Product Sales Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function product_sales()
    {
        Gate::authorize('view product sales report');

        return view('application.reports.product_sales');
    }

    /**
     * Get Profit & Loss Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function profit_loss()
    {
        Gate::authorize('view profit loss report');

        return view('application.reports.profit_loss');
    }

    /**
     * Get Expenses.
     *
     * @return \Illuminate\Http\Response
     */
    public function expenses()
    {
        Gate::authorize('view expenses report');

        return view('application.reports.expenses');
    }

    /**
     * Get Vendors.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendors()
    {
        Gate::authorize('view vendors report');

        return view('application.reports.vendors');
    }
}
