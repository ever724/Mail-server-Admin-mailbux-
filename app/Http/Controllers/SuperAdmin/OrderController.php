<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display Super Admin Orders Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get Orders
        $orders = QueryBuilder::for(Order::class)
            ->oldest()
            ->paginate()
            ->appends(request()->query());

        return view('super_admin.orders.index', [
            'orders' => $orders,
        ]);
    }
}
