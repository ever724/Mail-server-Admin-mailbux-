<?php

namespace App\Http\Controllers\Application\Settings;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PDF;

class MembershipController extends Controller
{
    /**
     * Display Membership Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view membership');

        $currentCompany = $request->currentCompany;
        $subscription = $currentCompany->subscription('main');
        $orders = Order::where('company_id', $currentCompany->id)->latest()->get();

        return view('application.settings.membership.index', [
            'subscription' => $subscription,
            'orders' => $orders,
            'dateFormat' => $currentCompany->getSetting('date_format'),
        ]);
    }

    /**
     * Display Invoice for Order.
     *
     * @return \Illuminate\Http\Response
     */
    public function order_invoice(Request $request)
    {
        Gate::authorize('view membership');

        $currentCompany = $request->currentCompany;
        $order = Order::where('order_id', $request->order_id)->firstOrFail();
        $subscription = $currentCompany->subscription('main');

        $pdf = PDF::loadView('pdf.order.invoice', [
            'order' => $order,
            'subscription' => $subscription,
            $currentCompany->getSetting('date_format'),
        ]);

        //Render
        return $pdf->stream('invoice.pdf');
    }
}
