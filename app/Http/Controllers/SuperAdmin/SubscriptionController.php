<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlanSubscription;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display Super Admin Subscriptions Page.
     *
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        // Get Subscriptions
        $subscriptions = QueryBuilder::for(PlanSubscription::class)
            ->oldest()
            ->paginate()
            ->appends(request()->query());

        return view('super_admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
