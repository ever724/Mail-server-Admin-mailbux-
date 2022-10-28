<?php

namespace App\Repositories;

use App\Interfaces\PlanInterface;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PlanRepository implements PlanInterface
{
    /**
     * Return results of Plans.
     */
    public function getPlans(Request $request): Collection
    {
        return Plan::query()
            ->where('is_active', 1)
            ->with('active_features')
            ->orderBy('order', 'ASC')
            ->get();
    }
}
